

<div id="tab-attendance" class="tab-content">
                <div class="mb-6">
                    <h1>Attendance Management</h1>
                    <p class="card-description">RFID + Biometric Face Verification System</p>
                </div>

                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 2fr 1fr;">
                    <!-- Live Tracking -->
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="card-title">Live Attendance Tracking</h3>
                                    <p class="card-description">Real-time attendance with dual verification</p>
                                </div>
                                <button class="btn btn-primary" id="scanRFID">
                                    <span class="icon">📱</span>
                                    <span class="ml-2">Scan RFID</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="verification-panel mb-6" id="verificationPanel">
                                <div class="text-center">
                                    <div class="icon icon-xl mb-4">📱</div>
                                    <h4>Ready for Verification</h4>
                                    <p class="card-description">Tap RFID card or click Scan RFID to begin</p>
                                </div>
                            </div>

                            <!-- Existing Live Tracking Table -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Hours</th>
                                        <th>Verification</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="liveAttendanceTable">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Column Widgets -->
                    <div class="space-y-6">
                        <!-- Verification Stats -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Verification Stats</h3>
                            </div>
                            <div class="card-content space-y-4">
                                <!-- ... existing content ... -->
                            </div>
                        </div>

                        <!-- Security Alerts -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Security Alerts</h3>
                            </div>
                            <div class="card-content">
                                <div class="text-center">
                                    <div class="icon icon-xl" style="color: var(--success);">✅</div>
                                    <p>No security violations detected</p>
                                    <p class="card-description">All verifications successful</p>
                                    <div class="mt-4">
                                        <div class="badge badge-success">Fraud Prevention: Active</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Scans -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Scans</h3>
                            </div>
                            <div class="card-content" id="recentScans">
                                <!-- ... existing content ... -->
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <!-- Manual Attendance Entry -->
                <div class="card mt-8">
                    <div class="card-header flex items-center justify-between">
                        <h3 class="card-title">Manual Attendance Entry</h3>
                        <button class="btn btn-primary" onclick="addManualRow()">+ Add Record</button>
                    </div>
                    <div class="card-content">
                        <table class="table" id="manualAttendanceTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic manual rows will go here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <script>


                function updatePosition(select) {
                    let row = select.closest("tr");
                    let positionInput = row.querySelector("input[name='position']");
                    let selected = select.options[select.selectedIndex];
                    positionInput.value = selected.getAttribute("data-position") || "";
                }


                function removeRow(button) {
                    button.closest("tr").remove();
                }

                function saveManualRow(button) {
                    const row = button.closest("tr");
                    const employee_id = row.querySelector("select[name='employee_id']").value;
                    const position = row.querySelector("input[name='position']").value;
                    const date = row.querySelector("input[name='date']").value;
                    const time = row.querySelector("input[name='time']").value;

                    if (!employee_id || !date || !time) {
                        alert("Please fill in all required fields.");
                        return;
                    }

                    fetch("save_attendance.php", {
                        method: "POST",
                        headers: {"Content-Type": "application/x-www-form-urlencoded"},
                        body: `employee_id=${employee_id}&position=${encodeURIComponent(position)}&date=${date}&time=${time}&method=manual`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        button.disabled = true;
                    })
                    .catch(err => console.error(err));
                }

                </script>
                <script>
                document.getElementById("employeeSelect").addEventListener("change", function() {
                    let selected = this.options[this.selectedIndex];
                    let position = selected.getAttribute("data-position") || "";
                    document.getElementById("employeePosition").value = position;
                });

                // Handle form submit via AJAX
                document.getElementById("manualAttendanceForm").addEventListener("submit", function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    fetch("save_attendance.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            // Reset form
                            this.reset();
                            document.getElementById("employeePosition").value = "";

                            // Append new row to Live Attendance Table
                            let table = document.getElementById("liveAttendanceTable");
                            let row = table.insertRow();

                            row.innerHTML = `
                                <td><strong>${data.full_name}</strong></td>
                                <td>${data.clock_in}</td>
                                <td>-</td>
                                <td class="live-time">0h</td>
                                <td>
                                    <div class="flex gap-1">
                                        <span class="badge badge-success">Manual</span>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">Present</span></td>
                            `;
                        }
                    })
                    .catch(err => console.error(err));
                });
                </script>
<script>
// function loadAttendance() {
//     fetch("fetch_attendance.php")
//         .then(res => res.json())
//         .then(data => {
//             const tbody = document.getElementById("liveAttendanceTable");
//             tbody.innerHTML = "";

//             data.forEach(row => {
//                 const clockIn  = row.clock_in ? new Date(row.clock_in).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : "-";
//                 const clockOut = row.clock_out ? new Date(row.clock_out).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : "-";

//                 // calculate hours if both times exist
//                 let hours = "-";
//                 if (row.clock_in && row.clock_out) {
//                     const inTime  = new Date(row.clock_in);
//                     const outTime = new Date(row.clock_out);
//                     const diffMs  = outTime - inTime;
//                     const diffH   = Math.floor(diffMs / (1000*60*60));
//                     const diffM   = Math.floor((diffMs % (1000*60*60)) / (1000*60));
//                     hours = `${diffH}h ${diffM}m`;
//                 }
// tbody.innerHTML += `
//     <tr>
//         <td><strong>${row.full_name}</strong></td>
//         <td>${clockIn}</td>
//         <td>${clockOut}</td>
//         <td class="live-time">${hours}</td>
//         <td>
//             <div class="flex gap-1">
//                 <span class="badge badge-success">${row.method}</span>
//             </div>
//         </td>
//         <td>
//             ${!row.clock_out 
//                 ? `<button class="btn btn-warning btn-sm" onclick="clockOut(${row.attendance_id})">Clock Out</button>` 
//                 : `<span class="badge badge-${row.status === 'present' ? 'success' : 'warning'}">
//                      ${row.status.charAt(0).toUpperCase() + row.status.slice(1)}
//                    </span>`
//             }
//         </td>
//     </tr>
// `;
//             });
//         })
//         .catch(err => console.error(err));
// }

// // Load immediately
// loadAttendance();

// // Refresh every 5 seconds
// setInterval(loadAttendance, 5000);

function clockOut(attendance_id) {
    if (!confirm("Are you sure you want to clock out this employee?")) return;

    fetch("clock_out.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `attendance_id=${attendance_id}`
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
        loadAttendance(); // refresh table
    })
    .catch(err => console.error(err));
}

</script>
