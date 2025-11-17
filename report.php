<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1️⃣ Analytics function
function getAnalyticsData($conn) {
    $totalClockIns = $conn->query("SELECT COUNT(*) as total FROM attendance")->fetch_assoc()['total'] ?? 0;
    $totalTimesheets = $conn->query("SELECT COUNT(*) as total FROM timesheets")->fetch_assoc()['total'] ?? 0;
    $approvedTimesheets = $conn->query("SELECT COUNT(*) as total FROM timesheets WHERE status='Approved'")->fetch_assoc()['total'] ?? 0;
    $successRate = $totalTimesheets > 0 ? round(($approvedTimesheets / $totalTimesheets) * 100) . '%' : '0%';
    $avgVerificationTime = '4.2s';
    $securityViolations = $conn->query("SELECT COUNT(*) as total FROM audit_trail WHERE table_name='security'")->fetch_assoc()['total'] ?? 0;

    return [
        'totalClockIns' => $totalClockIns,
        'successRate' => $successRate,
        'avgVerificationTime' => $avgVerificationTime,
        'securityViolations' => $securityViolations
    ];
}

$analytics = getAnalyticsData($conn);

// 2️⃣ Report function
function generateReportData($conn, $type) {
    switch($type) {
        case 'payroll':
            $data = $conn->query("
                SELECT COUNT(*) as totalPayrolls, 
                       SUM(gross_pay) as totalGross, 
                       SUM(net_pay) as totalNet 
                FROM payroll
            ")->fetch_assoc();
            return $data;

        case 'attendance':
            $data = $conn->query("
                SELECT COUNT(*) as totalClockIns, 
                       AVG(TIME_TO_SEC(TIMEDIFF(time_out, time_in))/3600) as avgHours 
                FROM attendance
            ")->fetch_assoc();
            return $data;

        case 'compliance':
            $data = $conn->query("
                SELECT COUNT(*) as totalRecords, 
                       SUM(CASE WHEN time_out IS NULL THEN 1 ELSE 0 END) as missingTimeOuts 
                FROM attendance
            ")->fetch_assoc();
            return $data;

        default:
            return [];
    }
}

// 3️⃣ Handle AJAX request
if(isset($_GET['report'])) {
    $reportType = $_GET['report'];
    $reportData = generateReportData($conn, $reportType);
    header('Content-Type: application/json');
    echo json_encode($reportData);
    exit;
}
?>
<style>
    table.excel-style {
  border-collapse: collapse;
  width: 100%;
  font-size: 14px;
}
table.excel-style th,
table.excel-style td {
  border: 1px solid #c0c0c0; /* Excel gray borders */
  padding: 6px 10px;
}
table.excel-style thead {
  background-color: #d9e1f2; /* Excel header blue */
  font-weight: bold;
}
table.excel-style tbody tr:nth-child(even) {
  background-color: #f8f9fa; /* Light gray for even rows */
}

</style>
<div id="tab-reports" class="tab-content">
    <div class="mb-6">
        <h1>Reports & Security</h1>
        <p class="card-description">Comprehensive reporting and security monitoring</p>
    </div>

    <div class="tabs">
        <div class="tab-list">
            <button class="tab-trigger active" data-subtab="reports">Reports</button>
            <button class="tab-trigger" data-subtab="analytics">Analytics</button>
        </div>

        <div id="subtab-reports" class="tab-content active">
            <div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(3, 1fr);">
                <div class="card">
                    <div class="card-content text-center">
                        <div class="icon icon-xl mb-4">📊</div>
                        <h4>Payroll Report</h4>
                        <p class="card-description">Detailed payroll breakdown and analysis</p>
                        <button class="btn btn-primary mt-4 w-full" onclick="generateReport('payroll')">Generate Report</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content text-center">
                        <div class="icon icon-xl mb-4">⏰</div>
                        <h4>Attendance Report</h4>
                        <p class="card-description">Attendance patterns and trends analysis</p>
                        <button class="btn btn-primary mt-4 w-full" onclick="generateReport('attendance')">Generate Report</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content text-center">
                        <div class="icon icon-xl mb-4">📋</div>
                        <h4>Compliance Report</h4>
                        <p class="card-description">Labor law compliance status verification</p>
                        <button class="btn btn-primary mt-4 w-full" onclick="generateReport('compliance')">Generate Report</button>
                    </div>
                </div>
            </div>

            <!-- 5️⃣ Place to display generated report -->
            <div id="reportResult" class="mt-6"></div>
        </div>

        <div id="subtab-analytics" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Usage Analytics</h3>
                    <p class="card-description">System performance and usage statistics</p>
                </div>
                <div class="card-content">
                    <div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(4, 1fr);">
                        <div class="text-center feature-highlight">
                            <div class="stat-value"><?= $analytics['totalClockIns'] ?></div>
                            <p class="card-description">Total Clock-ins</p>
                            <div class="mt-2">
                                <span class="badge badge-success">+12%</span>
                            </div>
                        </div>
                        <div class="text-center feature-highlight">
                            <div class="stat-value"><?= $analytics['successRate'] ?></div>
                            <p class="card-description">Success Rate</p>
                            <div class="mt-2">
                                <span class="badge badge-success">+2%</span>
                            </div>
                        </div>
                        <div class="text-center feature-highlight">
                            <div class="stat-value"><?= $analytics['avgVerificationTime'] ?></div>
                            <p class="card-description">Avg Verification Time</p>
                            <div class="mt-2">
                                <span class="badge badge-success">-0.3s</span>
                            </div>
                        </div>
                        <div class="text-center feature-highlight">
                            <div class="stat-value"><?= $analytics['securityViolations'] ?></div>
                            <p class="card-description">Security Violations</p>
                            <div class="mt-2">
                                <span class="badge badge-success">Perfect</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6️⃣ JS AJAX function -->
<script>
function generateReport(type) {
    fetch(`ajax_report.php?report=${type}`)
        .then(response => response.json())
        .then(data => {
            let html = `
            <div class="card mt-6 shadow-lg rounded-2xl p-6 border bg-white">
                <div class="card-header text-center mb-6">
                    <div class="icon text-5xl mb-3">${getIcon(type)}</div>
                    <h3 class="text-2xl font-bold">${capitalize(type)} Report</h3>
                    <p class="text-gray-500">Detailed ${capitalize(type)} records per employee</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-auto excel-style w-full border-collapse border border-gray-300 rounded-lg shadow-sm">
                        <thead class="bg-gray-100">
                            <tr>
            `;

            // Set headers dynamically
            if (type === "payroll") {
                html += `
                    <th class="px-4 py-2 border">Employee</th>
                    <th class="px-4 py-2 border">Total Hours</th>
                    <th class="px-4 py-2 border">Base Salary</th>
                    <th class="px-4 py-2 border">Overtime</th>
                    <th class="px-4 py-2 border">Deductions</th>
                    <th class="px-4 py-2 border">SSS</th>
                    <th class="px-4 py-2 border">PhilHealth</th>
                    <th class="px-4 py-2 border">Pag-IBIG</th>
                    <th class="px-4 py-2 border">Tax</th>
                    <th class="px-4 py-2 border">Gross</th>
                    <th class="px-4 py-2 border">Net</th>
                `;
            } else if (type === "attendance") {
                html += `
                    <th class="px-4 py-2 border">Employee</th>
                    <th class="px-4 py-2 border">Total Clock-ins</th>
                    <th class="px-4 py-2 border">Average Hours</th>
                `;
            } else if (type === "compliance") {
                html += `
                    <th class="px-4 py-2 border">Employee</th>
                    <th class="px-4 py-2 border">Total Records</th>
                    <th class="px-4 py-2 border">Missing Time-outs</th>
                `;
            }

            html += `</tr></thead><tbody>`;

            // If only one object is returned, wrap into array
            if (!Array.isArray(data)) {
                data = [data];
            }

            // Render rows
            data.forEach(emp => {
                if (type === "payroll") {
                    html += `
                        <tr class="text-center hover:bg-gray-50">
                            <td class="px-4 py-2 border font-medium">${emp.employee_name}</td>
                            <td class="px-4 py-2 border">${emp.totalHours ?? 0} hrs</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalBaseSalary).toFixed(2)}</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalOvertime).toFixed(2)}</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalDeductions).toFixed(2)}</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalSSS).toFixed(2)}</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalPhilhealth).toFixed(2)}</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalPagibig).toFixed(2)}</td>
                            <td class="px-4 py-2 border">P${parseFloat(emp.totalTax).toFixed(2)}</td>
                            <td class="px-4 py-2 border font-semibold">P${parseFloat(emp.totalGross).toFixed(2)}</td>
                            <td class="px-4 py-2 border font-bold text-green-600">P${parseFloat(emp.totalNet).toFixed(2)}</td>
                        </tr>
                    `;
                } else if (type === "attendance") {
                    html += `
                        <tr class="text-center hover:bg-gray-50">
                            <td class="px-4 py-2 border font-medium">${emp.employee_name}</td>
                            <td class="px-4 py-2 border">${emp.totalClockIns ?? 0}</td>
                            <td class="px-4 py-2 border">${parseFloat(emp.avgHours ?? 0).toFixed(2)} hrs</td>
                        </tr>
                    `;
                } else if (type === "compliance") {
                    html += `
                        <tr class="text-center hover:bg-gray-50">
                            <td class="px-4 py-2 border font-medium">${emp.employee_name}</td>
                            <td class="px-4 py-2 border">${emp.totalRecords ?? 0}</td>
                            <td class="px-4 py-2 border text-red-600">${emp.missingTimeOuts ?? 0}</td>
                        </tr>
                    `;
                }
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="card-footer mt-6 flex justify-center gap-4">
                    <a class="btn btn-secondary px-5 py-2 rounded-lg shadow-md hover:bg-gray-200 mb-6" href="ajax_report.php?report=${type}&export=pdf">📄 Export as PDF</a>
                    <!--<a class="btn btn-secondary px-5 py-2 rounded-lg shadow-md hover:bg-gray-200 mb-6" href="ajax_report.php?report=${type}&export=excel">📊 Download Excel</a>-->
                </div>
            </div>
            `;

            document.getElementById('reportResult').innerHTML = html;
        })
        .catch(error => console.error('Error fetching report:', error));
}

// Helpers
function getIcon(type) {
    switch(type) {
        case 'payroll': return "📊";
        case 'attendance': return "⏰";
        case 'compliance': return "📋";
        default: return "📄";
    }
}
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Helper: Format report content
function getReportContent(type, data) {
    if (type === 'payroll') {
        let rows = data.employees.map(emp => `
            <tr>
                <td class="p-2 border font-semibold">${emp.name}</td>
                <td class="p-2 border">${parseFloat(emp.totalHours).toFixed(2)} hrs</td>
                <td class="p-2 border">₱${parseFloat(emp.baseSalary).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(emp.overtime).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(emp.deductions).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(emp.sss).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(emp.philhealth).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(emp.pagibig).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(emp.withholdingTax).toFixed(2)}</td>
                <td class="p-2 border font-bold">₱${parseFloat(emp.grossPay).toFixed(2)}</td>
                <td class="p-2 border font-bold text-green-600">₱${parseFloat(emp.netPay).toFixed(2)}</td>
            </tr>
        `).join("");

        let totals = data.totals;
        let totalRow = `
            <tr class="bg-gray-200 font-bold">
                <td class="p-2 border">TOTAL</td>
                <td class="p-2 border">${parseFloat(totals.totalHours).toFixed(2)} hrs</td>
                <td class="p-2 border">₱${parseFloat(totals.baseSalary).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.overtime).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.deductions).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.sss).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.philhealth).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.pagibig).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.withholdingTax).toFixed(2)}</td>
                <td class="p-2 border">₱${parseFloat(totals.grossPay).toFixed(2)}</td>
                <td class="p-2 border text-green-700">₱${parseFloat(totals.netPay).toFixed(2)}</td>
            </tr>
        `;
        return rows + totalRow;
    }

    if (type === 'attendance') {
        let rows = data.employees.map(emp => `
            <tr>
                <td class="p-2 border font-semibold">${emp.name}</td>
                <td class="p-2 border">${emp.totalClockIns}</td>
                <td class="p-2 border">${parseFloat(emp.avgHours).toFixed(2)} hrs</td>
            </tr>
        `).join("");

        let totals = data.totals;
        let totalRow = `
            <tr class="bg-gray-200 font-bold">
                <td class="p-2 border">TOTAL</td>
                <td class="p-2 border">${totals.totalClockIns}</td>
                <td class="p-2 border">${parseFloat(totals.avgHours).toFixed(2)} hrs</td>
            </tr>
        `;
        return rows + totalRow;
    }

    if (type === 'compliance') {
        let rows = data.employees.map(emp => `
            <tr>
                <td class="p-2 border font-semibold">${emp.name}</td>
                <td class="p-2 border">${emp.totalRecords}</td>
                <td class="p-2 border text-red-600">${emp.missingTimeOuts}</td>
            </tr>
        `).join("");

        let totals = data.totals;
        let totalRow = `
            <tr class="bg-gray-200 font-bold">
                <td class="p-2 border">TOTAL</td>
                <td class="p-2 border">${totals.totalRecords}</td>
                <td class="p-2 border text-red-700">${totals.missingTimeOuts}</td>
            </tr>
        `;
        return rows + totalRow;
    }

    return "";
}



</script>


