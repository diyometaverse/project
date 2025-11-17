<?php
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<style>
        /* Header */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 22px;
        font-weight: bold;
        color: #6b7280;
        cursor: pointer;
    }
    .modal-close:hover {
        color: #111827;
    }

    /* Body */
    .modal-body {
        padding: 16px;
        font-size: 14px;
        color: #374151;
    }

    /* Footer */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding: 12px 16px;
    }
    .btn-close {
        padding: 6px 14px;
        border-radius: 6px;
        background: #e5e7eb;
        color: #374151;
        border: none;
        cursor: pointer;
    }
    .btn-close:hover {
        background: #d1d5db;
    }
.generate-modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.generate-modal.hidden {
  display: none;
}

.generate-modal .modal-content {
  background: #fff;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
  padding: 16px;
}

.status {
    padding: 4px 8px;
    border-radius: 10px;
    font-weight: 500;
    text-align: center;
    color: white;
    display: inline-block;
}

.status-approved {
    background-color: #4CAF50; /* green */
}

.status-rejected {
    background-color: #F44336; /* red */
}

.status-pending {
    background-color: #bebebdff; /* orange */
}



</style>
<div id="tab-timesheets" class="tab-content">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1>Timesheet Processing</h1>
                            <p class="card-description">Review and approve employee timesheets</p>
                        </div>
                        <button class="btn btn-primary" onclick="approveAllTimesheets()">
                            <span class="icon">✅</span>
                            <span class="ml-2">Approve All</span>
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="card-title">Pending Timesheets</h3>
                                <p class="card-description">12 timesheets awaiting approval</p>
                            </div>
                            <div class="flex gap-2">
                            <button id="generateBtn" class="btn btn-ghost">
                                <span class="icon">🔄</span>
                                <span class="ml-2">Generate</span>
                            </button>
                            <button id="addPeriodBtn" class="btn btn-ghost">
                                <span class="icon">🔄</span>
                                <span class="ml-2">Add Period</span>
                            </button>
                            <!-- Add Period -->
                            <div id="addPeriodModal" class="modal hidden">
                                <div class="modal-content card">
                                    <div class="card-header">
                                        <h3 class="card-title">Add Payroll Period</h3>
                                    </div>
                                    <div class="card-content">
                                        <form id="addPeriodForm" method="POST" action="">
                                            <div class="space-y-2">
                                                <!-- <label for="label">Label</label>
                                                <input type="text" id="label" name="label" class="input" placeholder="Ex: Sep 1-15, 2025" required> -->
                                            </div>
                                            <div class="space-y-2">
                                                <label for="startDate">Start Date</label>
                                                <input type="date" id="startDate" name="start_date" class="input" required>
                                            </div>
                                            <div class="space-y-2">
                                                <label for="endDate">End Date</label>
                                                <input type="date" id="endDate" name="end_date" class="input" required readonly>
                                            </div>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" class="btn btn-ghost" id="closeModalBtn">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Add Period</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Generate Period Modal -->
                            <div id="choosePeriodModal" class="generate-modal hidden">
                            <div class="modal-content">
                                <!-- Header -->
                                <div class="modal-header">
                                <h5 class="modal-title">Select Payroll Period</h5>
                                <button type="button" class="btn-close" onclick="closeGenerateModal()">×</button>
                                </div>

                                <!-- Body -->
                                <div class="modal-body">
                                <ul class="list-group">
                                <?php
                                include 'db.php';

                                $sql = "SELECT period_id, start_date, end_date FROM payroll_periods ORDER BY start_date DESC";
                                $result = $conn->query($sql);

                                while ($row = $result->fetch_assoc()) :
                                    $periodId = $row['period_id'];
                                    $startDate = date("M j, Y", strtotime($row['start_date']));
                                    $endDate = date("M j, Y", strtotime($row['end_date']));
                                ?>
                                    <li class="list-group-item">
                                        <button 
                                            onclick="generateTimesheets(<?= $periodId ?>)" 
                                            class="text-decoration-none"
                                        >
                                            <?= $startDate ?> - <?= $endDate ?>
                                        </button>
                                    </li>
                                <?php endwhile; ?>
                                </ul>

                                </div>
                            </div>
                            </div>
                                <!-- <button class="btn btn-ghost">
                                    <span class="icon">📅</span>
                                    <span class="ml-2">Choose Period</span>
                                </button> -->
                                <button id="filterBtn" class="btn btn-ghost">
                                    <span class="icon">📅</span>
                                    <span class="ml-2">Filter</span>
                                </button>
                                <button id="exportBtn" class="btn btn-ghost">
                                    <span class="icon">📤</span>
                                    <span class="ml-2">Export</span>
                                </button>

                                <!-- Export Modal -->
                                <div id="exportModal" class="generate-modal hidden">
                                    <div class="modal-content">
                                        <!-- Header -->
                                        <div class="modal-header">
                                            <h5 class="modal-title">Export Timesheets</h5>
                                            <button type="button" class="btn-close" onclick="closeExportModal()">×</button>
                                        </div>

                                        <!-- Body -->
                                        <div class="modal-body">
                                            <p>Select export format:</p>
                                            <div class="flex flex-col gap-2 mt-2">
                                                <!-- <button class="btn btn-primary" onclick="exportToExcel()">📊 Export as Excel</button> -->
                                                <button class="btn btn-primary" onclick="exportToPDF()">📄 Export as PDF</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Filter Modal -->
                                <div id="filterModal" class="generate-modal hidden">
                                    <div class="modal-content">
                                        <!-- Header -->
                                        <div class="modal-header">
                                            <h5 class="modal-title">Filter Timesheets</h5>
                                        </div>

                                        <!-- Body -->
                                        <div class="modal-body flex flex-col gap-3">
                                            <label>Employee</label>
                                            <select id="filterEmployee" class="btn btn-ghost">
                                                <option value="">All Employees</option>
                                                <?php
                                                $empSql = "SELECT employee_id, first_name, last_name FROM employees ORDER BY first_name";
                                                $empResult = $conn->query($empSql);
                                                while($emp = $empResult->fetch_assoc()) {
                                                    echo '<option value="'.$emp['employee_id'].'">'.$emp['first_name'].' '.$emp['last_name'].'</option>';
                                                }
                                                ?>
                                            </select>

                                            <label>Status</label>
                                            <select id="filterStatus" class="btn btn-ghost">
                                                <option value="">All Status</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>

                                            <label>Date From</label>
                                            <input type="date" id="filterDateFrom" class="btn btn-ghost">

                                            <label>Date To</label>
                                            <input type="date" id="filterDateTo" class="btn btn-ghost">
                                        </div>

                                        <!-- Footer -->
                                        <div class="modal-footer">
                                            <button class="btn btn-primary" id="applyFilter">Apply</button>
                                            <button class="btn-close" onclick="closeFilterModal()">Close</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Period</th>
                                    <th>Total Hours</th>
                                    <th>Regular</th>
                                    <th>Overtime</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="timesheetTable">

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal -->
                <div id="timesheetModal" class="modal hidden">
                    <div class="modal-content">
                        <!-- Header -->
                        <div class="modal-header">
                            <h2 class="modal-title">Timesheet Details</h2>
                        </div>

                        <!-- Body -->
                        <div id="timesheetDetails" class="modal-body">
                            <!-- Timesheet data loads here -->
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button onclick="closeModal()" class="btn-close">Close</button>
                        </div>
                    </div>
                </div>

            </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
            <script>
document.addEventListener("DOMContentLoaded", () => {
    // Load timesheets initially
    loadTimesheets();

    // Open generate modal
    document.getElementById("generateBtn").addEventListener("click", () => {
        document.getElementById("choosePeriodModal").classList.remove("hidden");
    });

    // Open filter modal
    document.getElementById("filterBtn").addEventListener("click", () => {
        document.getElementById("filterModal").classList.remove("hidden");
    });

    // Apply filter
    document.getElementById("applyFilter").addEventListener("click", () => {
        applyFilter();
    });
});

const startDateInput = document.getElementById('startDate');
const endDateInput = document.getElementById('endDate');

startDateInput.addEventListener('change', () => {
    const startDate = new Date(startDateInput.value);
    if (isNaN(startDate)) return;

    // Example: 15-day period, so end date = start + 14 days
    const endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + 15);

    // Format as yyyy-mm-dd for input[type=date]
    const yyyy = endDate.getFullYear();
    const mm = String(endDate.getMonth() + 1).padStart(2, '0');
    const dd = String(endDate.getDate()).padStart(2, '0');
    endDateInput.value = `${yyyy}-${mm}-${dd}`;
});


const addPeriodBtn = document.getElementById('addPeriodBtn');
const addPeriodModal = document.getElementById('addPeriodModal');
const closeModalBtn = document.getElementById('closeModalBtn');

addPeriodBtn.addEventListener('click', () => {
    addPeriodModal.classList.remove('hidden');
});

closeModalBtn.addEventListener('click', () => {
    addPeriodModal.classList.add('hidden');
});

// Optional: Close modal when clicking outside the content
addPeriodModal.addEventListener('click', (e) => {
    if (e.target === addPeriodModal) {
        addPeriodModal.classList.add('hidden');
    }
});

function closeGenerateModal() {
    document.getElementById("choosePeriodModal").classList.add("hidden");
}

function generateTimesheets(periodId) {
    fetch("generate_timesheets.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "period_id=" + periodId
    })
    .then(res => res.json())
    .then(data => {
        showNotification(data.message, data.success ? "success" : "error");
        closeGenerateModal();
        loadTimesheets(); // refresh table without reload
    })
    .catch(err => console.error(err));
}

function loadTimesheets() {
    fetch("timesheet_list.php")
        .then(res => res.json())
        .then(data => {
            let tbody = document.getElementById("timesheetTable");
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">No timesheets found</td></tr>`;
                return;
            }

            data.forEach(t => {
                    let statusClass = '';
                    switch (t.status.toLowerCase()) {
                        case 'approved':
                            statusClass = 'status-approved';
                            break;
                        case 'rejected':
                            statusClass = 'status-rejected';
                            break;
                        case 'pending':
                            statusClass = 'status-pending';
                            break;
                    }
                tbody.innerHTML += `
                    <tr>
                        <td>${t.first_name} ${t.last_name}</td>
                        <td>${t.work_date}</td>
                        <td>${t.hours_worked}</td>
                        <td>${Math.min(t.hours_worked, 8)}</td>
                        <td>${t.overtime_hours}</td>
                        <td><span class="status ${statusClass}">${t.status}</span></td>
                        <td>
                            <button class="btn btn-ghost" onclick="viewTimesheet(${t.employee_id}, '${t.work_date}')">🔍</button>
                            <button class="btn btn-ghost" onclick="approveTimesheet(${t.timesheet_id})">✅</button>
                            <button class="btn btn-ghost" onclick="rejectTimesheet(${t.timesheet_id})">❌</button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error(err);
        });
}

document.addEventListener("DOMContentLoaded", loadTimesheets);


function approveAllTimesheets() {
    fetch("approve_all_timesheets.php", { method: "POST" })
        .then(res => res.json())
        .then(data => showNotification(data.message, data.success ? "success" : "error"))
        .finally(loadTimesheets);
}

function approveTimesheet(timesheetId){
    fetch("approve_timesheet.php", {
        method: "POST",
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `timesheet_id=${timesheetId}`
    })
    .then(res => res.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success':'error');
        loadTimesheets();
    });
}

function rejectTimesheet(timesheetId){
    fetch("reject_timesheet.php", {
        method: "POST",
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `timesheet_id=${timesheetId}`
    })
    .then(res => res.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success':'error');
        loadTimesheets();
    });
}

// Optional notification popup
function showNotification(message, type) {
    alert(`[${type.toUpperCase()}] ${message}`);
}

function viewTimesheet(employeeId, workDate) {
    fetch(`view_timesheet.php?employee_id=${employeeId}&work_date=${workDate}`)
        .then(res => res.text())
        .then(data => {
            document.getElementById("timesheetDetails").innerHTML = data;
            document.getElementById("timesheetModal").classList.remove("hidden");
        })
        .catch(err => alert("Error loading timesheet!"));
}

function closeModal() {
    document.getElementById("timesheetModal").classList.add("hidden");
}

function closeFilterModal() {
    document.getElementById("filterModal").classList.add("hidden");
}


function applyFilter() {
    const employeeId = document.getElementById("filterEmployee").value;
    const status = document.getElementById("filterStatus").value;
    const dateFrom = document.getElementById("filterDateFrom").value;
    const dateTo = document.getElementById("filterDateTo").value;

    fetch("timesheet_list.php")
        .then(res => res.json())
        .then(data => {
            let filtered = data;

            if (employeeId) {
                filtered = filtered.filter(t => t.employee_id == employeeId);
            }

            if (status) {
                filtered = filtered.filter(t => t.status.toLowerCase() == status.toLowerCase());
            }

            if (dateFrom) {
                filtered = filtered.filter(t => t.work_date >= dateFrom);
            }

            if (dateTo) {
                filtered = filtered.filter(t => t.work_date <= dateTo);
            }

            renderTimesheets(filtered);
            closeFilterModal();
        });
}

function renderTimesheets(data) {
    let tbody = document.getElementById("timesheetTable");
    tbody.innerHTML = "";

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">No timesheets found</td></tr>`;
        return;
    }

    data.forEach(t => {
        let statusClass = '';
        switch (t.status.toLowerCase()) {
            case 'approved':
                statusClass = 'status-approved';
                break;
            case 'rejected':
                statusClass = 'status-rejected';
                break;
            case 'pending':
                statusClass = 'status-pending';
                break;
        }

        tbody.innerHTML += `
            <tr>
                <td>${t.first_name} ${t.last_name}</td>
                <td>${t.work_date}</td>
                <td>${t.hours_worked}</td>
                <td>${Math.min(t.hours_worked, 8)}</td>
                <td>${t.overtime_hours}</td>
                <td><span class="status ${statusClass}">${t.status}</span></td>
                <td>
                    <button class="btn btn-ghost" onclick="viewTimesheet(${t.employee_id}, '${t.work_date}')">🔍</button>
                    <button class="btn btn-ghost" onclick="approveTimesheet(${t.timesheet_id})">✅</button>
                    <button class="btn btn-ghost" onclick="rejectTimesheet(${t.timesheet_id})">❌</button>
                </td>
            </tr>
        `;
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // Open export modal
    document.getElementById("exportBtn").addEventListener("click", () => {
        document.getElementById("exportModal").classList.remove("hidden");
    });
});

function closeExportModal() {
    document.getElementById("exportModal").classList.add("hidden");
}

// Handle export
function exportTimesheets(format) {
    // You can replace this with an actual backend export file later
    window.location.href = "export_timesheets.php?format=" + format;

    closeExportModal();
}

// ✅ Export to Excel (excluding Actions column)
function exportToExcel() {
    const table = document.querySelector("#timesheetTable");
    const wb = XLSX.utils.book_new();

    // Convert table to array manually, excluding the last column
    const wsData = [];
    const headers = Array.from(table.closest("table").querySelectorAll("thead th"))
        .map((th, i, arr) => i < arr.length - 1 ? th.innerText.trim() : null)
        .filter(Boolean);
    wsData.push(headers);

    table.querySelectorAll("tr").forEach(row => {
        const rowData = Array.from(row.querySelectorAll("td"))
            .map((td, i, arr) => i < arr.length - 1 ? td.innerText.trim() : null)
            .filter(Boolean);
        if (rowData.length > 0) wsData.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(wsData);
    XLSX.utils.book_append_sheet(wb, ws, "Timesheets");
    XLSX.writeFile(wb, "Timesheets.xlsx");
    closeExportModal();
}

// ✅ Export to PDF (excluding Actions column)
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Timesheet Report", 14, 10);

    // Build data array manually
    const table = document.querySelector("#timesheetTable");
    const headers = Array.from(table.closest("table").querySelectorAll("thead th"))
        .map((th, i, arr) => i < arr.length - 1 ? th.innerText.trim() : null)
        .filter(Boolean);
    const body = [];

    table.querySelectorAll("tr").forEach(row => {
        const rowData = Array.from(row.querySelectorAll("td"))
            .map((td, i, arr) => i < arr.length - 1 ? td.innerText.trim() : null)
            .filter(Boolean);
        if (rowData.length > 0) body.push(rowData);
    });

    doc.autoTable({
        head: [headers],
        body: body,
        startY: 20,
        styles: { fontSize: 9 },
        headStyles: { fillColor: [37, 99, 235] } // blue accent
    });

    doc.save("Timesheets.pdf");
    closeExportModal();
}

const addPeriodForm = document.getElementById('addPeriodForm');

addPeriodForm.addEventListener('submit', function(e) {
    e.preventDefault(); // prevent page reload

    const start_date = document.getElementById('startDate').value;
    const end_date = document.getElementById('endDate').value;

    fetch('add_period.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `start_date=${start_date}&end_date=${end_date}`
    })
    .then(res => res.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');

        if(data.success){
            addPeriodForm.reset();
            addPeriodModal.classList.add('hidden'); // close modal

            // Refresh the modal list
            loadPayrollPeriods();

            // Refresh the payroll dropdown in your payroll page
            refreshPayrollPeriods();

            // Optional: reload timesheet table if needed
            loadTimesheets();

            loadPayrollPeriodModal();

        }
    })
    .catch(err => showNotification('Error adding period', 'error'));
});


// Function to reload payroll periods in the modal
function loadPayrollPeriods() {
    const ul = document.querySelector('.list-group');
    fetch('get_periods.php') // this PHP file should return JSON with period_id, start_date, end_date
        .then(res => res.json())
        .then(periods => {
            ul.innerHTML = '';
            periods.forEach(p => {
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                const startFormatted = new Date(p.start_date).toLocaleDateString('en-US', {month:'short', day:'numeric'});
                const endFormatted = new Date(p.end_date).toLocaleDateString('en-US', {month:'short', day:'numeric'});
                li.innerHTML = `<button onclick="generateTimesheets(${p.period_id})" class="text-decoration-none">
                                    ${startFormatted} - ${endFormatted}
                                </button>`;
                ul.appendChild(li);
            });
        });
}



</script>
