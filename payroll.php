<?php
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current payroll period
$period_sql = "SELECT period_id FROM payroll_periods ORDER BY end_date DESC LIMIT 1";
$period_result = $conn->query($period_sql);
$period = $period_result->fetch_assoc();
$period_id = $period['period_id'];

// Sum each statutory deduction for this payroll period
$sql = "SELECT 
            SUM(sss) as total_sss,
            SUM(philhealth) as total_philhealth,
            SUM(pagibig) as total_pagibig,
            SUM(withholding_tax) as total_tax
        FROM payroll
        WHERE period_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $period_id);
$stmt->execute();
$totals = $stmt->get_result()->fetch_assoc();

// Calculate total deductions
$total_deductions = $totals['total_sss'] + $totals['total_philhealth'] + $totals['total_pagibig'] + $totals['total_tax'];

$sss_width = $total_deductions > 0 ? ($totals['total_sss'] / $total_deductions) * 100 : 0;
$philhealth_width = $total_deductions > 0 ? ($totals['total_philhealth'] / $total_deductions) * 100 : 0;
$pagibig_width = $total_deductions > 0 ? ($totals['total_pagibig'] / $total_deductions) * 100 : 0;
$tax_width = $total_deductions > 0 ? ($totals['total_tax'] / $total_deductions) * 100 : 0;



?>

<style>
    .deduction-details {
    display: none;
    }
    tr:hover .deduction-details {
        display: block;
    }
    /* Modal overlay */
    .generate-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* dimmed background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        transition: opacity 0.3s ease;
    }

    .generate-modal.hidden {
        display: none;
        opacity: 0;
    }

    /* Modal content box */
    .generate-modal .modal-content {
        background: #fff;
        border-radius: 12px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: fadeIn 0.3s ease;
    }

    /* Modal header */
    .generate-modal .modal-header {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
    }

    .generate-modal .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .generate-modal .btn-close {
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: #333;
    }

    /* Modal body */
    .generate-modal .modal-body {
        padding: 1rem 1.5rem;
        max-height: 300px;
        overflow-y: auto;
    }

    /* List group */
    .generate-modal .list-group {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .generate-modal .list-group-item {
        margin-bottom: 0.5rem;
    }

    .generate-modal .list-group-item button {
        width: 100%;
        text-align: left;
        padding: 0.6rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        font-size: 0.95rem;
    }

    .generate-modal .list-group-item button:hover {
        background: #e0e0ff; /* subtle hover */
        transform: translateY(-2px);
    }

    /* Fade-in animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .mb-4 {
    margin-bottom: 1rem;
    }

    /* Label / description */
    .card-description {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 0.3rem;
        display: block;
    }

    /* Select dropdown */
    #periodSelect {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        color: #333;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    /* Hover and focus effect */
    #periodSelect:hover {
        border-color: #888;
    }

    #periodSelect:focus {
        border-color: #5b5bff;
        box-shadow: 0 0 0 2px rgba(91, 91, 255, 0.2);
    }
</style>
<div id="tab-payroll" class="tab-content">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1>Payroll Processing</h1>
                            <p class="card-description">Bi-weekly payroll with Philippine statutory deductions</p>
                        </div>
                        <div class="flex gap-2">
                            <button id="generatePayrollBtn" class="btn btn-primary">
                                <span class="icon">💰</span>
                                <span class="ml-2">Generate Payroll</span>
                            </button>
                            <!-- Generate Period Modal -->
                            <div id="choosePayrollPeriodModal" class="generate-modal hidden">
                                <div class="modal-content">
                                    <!-- Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select Payroll Period</h5>
                                        <button type="button" class="btn-close" onclick="closePayrollModal()">×</button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body">
                                        <ul class="list-group" id="payrollPeriodList">
                                            <!-- Periods will be loaded here dynamically -->
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 1fr 1fr;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Current Payroll Period</h3>
                            <!-- <p class="card-description">December 16-29, 2024</p> -->
                        <?php
                        // Fetch all payroll periods
                        $periods_sql = "SELECT period_id, start_date, end_date FROM payroll_periods ORDER BY end_date DESC";
                        $periods_result = $conn->query($periods_sql);
                        ?>

                        <div class="mb-4">
                            <p for="periodSelect" class="card-description">Select Payroll Period:</p>
                            <select id="periodSelect" class="border px-2 py-1 rounded">
                                <option value="all">All Period</option>
                                <?php while($row = $periods_result->fetch_assoc()): ?>
                                    <option value="<?= $row['period_id'] ?>">
                                        <?= date("M d, Y", strtotime($row['start_date'])) ?> - <?= date("M d, Y", strtotime($row['end_date'])) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        </div>


                        <div class="card-content">
                        <table id="payrollTable" class="table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Gross Pay</th>
                                    <th>Deductions</th>
                                    <th>Net Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will replace this -->
                            </tbody>
                        </table>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Philippine Statutory Deductions</h3>
                            <p class="card-description">Labor law compliance breakdown</p>
                        </div>
                        <div class="card-content space-y-4">
                            <div>
                                <div class="flex justify-between">
                                    <span>SSS Contributions</span>
                                    <span class="sss-amount">₱0.00</span>
                                </div>
                                <p class="card-description">Social Security System</p>
                                <div class="progress mt-2">
                                    <div class="progress-fill sss-fill"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between">
                                    <span>PhilHealth Contributions</span>
                                    <span class="philhealth-amount">₱0.00</span>
                                </div>
                                <p class="card-description">Philippine Health Insurance</p>
                                <div class="progress mt-2">
                                    <div class="progress-fill philhealth-fill"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between">
                                    <span>Pag-IBIG Contributions</span>
                                    <span class="pagibig-amount">₱0.00</span>
                                </div>
                                <p class="card-description">Home Development Mutual Fund</p>
                                <div class="progress mt-2">
                                    <div class="progress-fill pagibig-fill"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between">
                                    <span>Withholding Tax</span>
                                    <span class="tax-amount">₱0.00</span>
                                </div>
                                <p class="card-description">Bureau of Internal Revenue</p>
                                <div class="progress mt-2">
                                    <div class="progress-fill tax-fill"></div>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <div class="flex justify-between">
                                    <strong>Total Deductions</strong>
                                    <span class="total-deductions-amount">₱0.00</span>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-fill" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-6">
                    <div class="card-header">
                        <h3 class="card-title">Payroll Summary - Bi-weekly Period</h3>
                        <p class="card-description">Complete financial breakdown for research team</p>
                    </div>
                    <div class="card-content">
                        <div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(4, 1fr);">
                        <div class="text-center feature-highlight">
                            <div class="stat-value total-gross">₱0.00</div>
                            <p class="card-description">Total Gross Pay</p>
                        </div>
                        <div class="text-center feature-highlight">
                            <div class="stat-value total-deductions">₱0.00</div>
                            <p class="card-description">Total Deductions</p>
                        </div>
                        <div class="text-center feature-highlight">
                            <div class="stat-value total-net">₱0.00</div>
                            <p class="card-description">Total Net Pay</p>
                        </div>
                        <div class="text-center feature-highlight">
                            <div class="stat-value total-employees">0</div>
                            <p class="card-description">Team Members</p>
                        </div>
                        </div>
                    </div>
                </div>

            </div>
            <script>
            function loadPayroll() {
                let period = document.getElementById("periodSelect").value;

                fetch("payroll_list.php?period=" + period)
                    .then(res => res.json())
                    .then(data => {
                        let tbody = document.querySelector("#payrollTable tbody");
                        tbody.innerHTML = "";

                        if (data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="4" class="text-center">No payroll data found</td></tr>`;
                            return;
                        }

                        data.forEach(p => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${p.first_name} ${p.last_name}</td>
                                    <td>₱${Number(p.gross_pay).toFixed(2)}</td>
                                    <td>
                                        ₱${Number(p.deductions).toFixed(2)}
                                        <div class="deduction-details" style="font-size:12px; color:#555;">
                                            SSS: ₱${Number(p.sss).toFixed(2)}<br>
                                            PhilHealth: ₱${Number(p.philhealth).toFixed(2)}<br>
                                            Pag-IBIG: ₱${Number(p.pagibig).toFixed(2)}<br>
                                            Withholding Tax: ₱${Number(p.withholding_tax).toFixed(2)}
                                        </div>
                                    </td>
                                    <td>₱${Number(p.net_pay).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    })
                    .catch(err => console.error(err));
            }

            document.getElementById("periodSelect").addEventListener("change", () => {
                loadPayroll();
                loadPayrollTotals();
            });

            document.addEventListener("DOMContentLoaded", () => {
                loadPayroll();
                loadPayrollTotals();
            });
            function refreshPayrollPeriods() {
                fetch('get_periods.php') // new PHP endpoint that returns all periods as JSON
                    .then(res => res.json())
                    .then(periods => {
                        const select = document.getElementById('periodSelect');
                        select.innerHTML = '<option value="all">All Period</option>'; // reset options

                        periods.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.period_id;
                            option.textContent = formatDate(p.start_date) + ' - ' + formatDate(p.end_date);
                            select.appendChild(option);
                        });
                    });
            }

            function formatDate(dateStr) {
                const d = new Date(dateStr);
                const options = { month: 'short', day: 'numeric', year: 'numeric' };
                return d.toLocaleDateString('en-US', options);
            }


            function loadPayrollTotals() {
                let period = document.getElementById("periodSelect").value;

                fetch("payroll_totals.php?period=" + period)
                    .then(res => res.json())
                    .then(totals => {
                        // Update payroll summary
                        document.querySelector(".total-gross").textContent = "₱" + Number(totals.total_gross || 0).toFixed(2);
                        document.querySelector(".total-deductions").textContent = "₱" + Number(totals.total_deductions || 0).toFixed(2);
                        document.querySelector(".total-net").textContent = "₱" + Number(totals.total_net || 0).toFixed(2);
                        document.querySelector(".total-employees").textContent = totals.total_employees || 0;

                        // Update statutory deductions
                        const totalDeductions = Number(totals.total_sss || 0) + Number(totals.total_philhealth || 0) + Number(totals.total_pagibig || 0) + Number(totals.total_tax || 0);

                        document.querySelector(".sss-fill").style.width = totalDeductions ? (totals.total_sss / totalDeductions) * 100 + "%" : "0%";
                        document.querySelector(".philhealth-fill").style.width = totalDeductions ? (totals.total_philhealth / totalDeductions) * 100 + "%" : "0%";
                        document.querySelector(".pagibig-fill").style.width = totalDeductions ? (totals.total_pagibig / totalDeductions) * 100 + "%" : "0%";
                        document.querySelector(".tax-fill").style.width = totalDeductions ? (totals.total_tax / totalDeductions) * 100 + "%" : "0%";

                        // Update amounts
                        document.querySelector(".sss-amount").textContent = "₱" + Number(totals.total_sss || 0).toFixed(2);
                        document.querySelector(".philhealth-amount").textContent = "₱" + Number(totals.total_philhealth || 0).toFixed(2);
                        document.querySelector(".pagibig-amount").textContent = "₱" + Number(totals.total_pagibig || 0).toFixed(2);
                        document.querySelector(".tax-amount").textContent = "₱" + Number(totals.total_tax || 0).toFixed(2);
                        document.querySelector(".total-deductions-amount").textContent = "₱" + Number(totalDeductions).toFixed(2);
                    })
                    .catch(err => console.error(err));
            }



                // Show modal
                document.getElementById("generatePayrollBtn").addEventListener("click", () => {
                    document.getElementById("choosePayrollPeriodModal").classList.remove("hidden");
                });

                // Close modal
                function closePayrollModal() {
                    document.getElementById("choosePayrollPeriodModal").classList.add("hidden");
                }

                // Generate payroll for selected period
                function generatePayroll(periodId) {
                    fetch("generate_payroll.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "period_id=" + periodId
                    })
                    .then(res => res.json()) // parse JSON response
                    .then(data => {
                        // show your custom notification
                        showNotification(data.message, data.success ? "success" : "error");

                        // close modal
                        closePayrollModal();

                        // refresh payroll table & totals
                        loadPayroll();
                        loadPayrollTotals();
                    })
                    .catch(err => console.error(err));
                }

                function loadPayrollPeriodModal() {
                    const ul = document.getElementById('payrollPeriodList');

                    fetch('get_periods.php') // return periods as JSON
                        .then(res => res.json())
                        .then(periods => {
                            ul.innerHTML = ''; // clear existing list
                            periods.forEach(p => {
                                const li = document.createElement('li');
                                li.classList.add('list-group-item');
                                const start = formatDate(p.start_date);
                                const end = formatDate(p.end_date);
                                li.innerHTML = `<button onclick="generatePayroll(${p.period_id})" class="text-decoration-none">
                                                    ${start} - ${end}
                                                </button>`;
                                ul.appendChild(li);
                            });
                        });
                }

                // Call this whenever you add a new period
                // Example: after addPeriodForm submission
                loadPayrollPeriodModal();

                // Optional: formatDate function (reuse from your payroll dropdown)
                function formatDate(dateStr) {
                    const d = new Date(dateStr);
                    const options = { month: 'short', day: 'numeric', year: 'numeric' };
                    return d.toLocaleDateString('en-US', options);
                }

                function closePayrollModal() {
                    document.getElementById("choosePayrollPeriodModal").classList.add("hidden");
}




            </script>