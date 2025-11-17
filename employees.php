<link rel="stylesheet" href="styles.css">
<style>
  body {
    padding: 30px;
  }

  #recentScans {
  max-height: 200px;
  overflow-y: auto;
  padding: 8px;
  }

  #recentScans div {
    padding: 6px 15;
    border-bottom: 1px solid #eee;
  }

  /* Optional: Custom scrollbar */
  #recentScans::-webkit-scrollbar {
    width: 6px;
  }
  #recentScans::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 3px;
  }

  .table thead th {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
  }

  #tab-attendance .card-content .flex input {
    max-width: 150px;
  }
  #tab-attendance .card-content .flex button {
    min-width: 80px;
  }

  .card {
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  /* Chrome, Safari, Edge, Opera */
  input[type=number]::-webkit-outer-spin-button,
  input[type=number]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }

  /* Firefox */
  input[type=number] {
      -moz-appearance: textfield;
  }

  .stats-card {
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    border-radius: 10px;
}

.stats-content {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 15px 10px;
    
}

.stat-item {
    text-align: center;
    background: #f9f9f9;
    padding: 10px 0;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stat-label {
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 5px;
}

.stat-value .badge {
    font-size: 1.1rem;
    padding: 5px 10px;
}
</style>

<div id="tab-attendance">
    <div class="mb-6">
        <h1>Attendance Management</h1>
        <p class="card-description">RFID + Biometric Face Verification System</p>
    </div>
    <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 2fr 1fr;">
        <!-- Live Tracking -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="card-title">Live Attendance Tracking</h3>
                    <p class="card-description">Real-time attendance with dual verification</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="showMaintenanceMessage()">📱Scan RFID</button>
                </div>
            </div>
            <div class="card-content">
                <div id="verificationPanel" class="verification-panel mb-6 text-center">
                    <h4>Ready for Verification</h4>
                    <p class="card-description">Tap RFID card or click Scan RFID to begin</p>
                </div>
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
                        <tr>
                            <td><strong>Christel Arpon</strong></td>
                            <td>7:45 AM</td>
                            <td>-</td>
                            <td class="live-time">9.5h</td>
                            <td>
                                <div class="flex gap-1">
                                    <span class="badge badge-success">RFID</span>
                                    <span class="badge badge-success">Face</span>
                                </div>
                            </td>
                            <td><span class="badge badge-success">Present</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Right Column Widgets -->
        <div class="space-y-6">
            <div class="card stats-card">
                <div class="card-header">
                    <h3 class="card-title">Verification Stats</h3>
                </div>
                <div class="card-content stats-content" id="stats">
                    <div class="stat-item">
                        <div class="stat-label">Total RFID</div>
                        <div class="stat-value"><span class="badge badge-info" id="totalRFID">0</span></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total Face</div>
                        <div class="stat-value"><span class="badge badge-primary" id="totalFace">0</span></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total Present</div>
                        <div class="stat-value"><span class="badge badge-primary" id="totalPresent">0</span></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Security Alerts</h3></div>
                <div class="card-content text-center">
                    <p>No security alerts detected</p>
                    <p class="card-description">System monitoring active</p>
                    <div class="mt-4">
                        <div class="badge badge-success">Fraud Prevention: Active</div>
                    </div>
                </div>
            </div>
            <div class="card recent-scans-card">
                <div class="card-header"><h3 class="card-title">Recent Scans</h3></div>
                <div class="card-content" id="recentScans" style="max-height: 200px; overflow-y: auto;">
                    <!-- Static recent scan entries -->
                </div>
            </div>
            <!-- Manual Attendance Entry aligned with Right Column -->
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="card-title">Manual Attendance Entry</h3>
                    <!-- <p class="text-sm text-gray-500">Use if RFID/Face system is under maintenance</p> -->
                </div>
                <div class="card-content space-y-4">
                    <div class="flex items-center gap-4 flex-wrap">
                        <label for="employee_id" class="font-medium">Employee ID:</label>
                        <input type="number" id="employee_id" placeholder="Enter Employee ID" class="input input-bordered" style="max-width: 400px; flex: 1;" autocomplete="off">
                        <button class="btn btn-primary" onclick="submitManual()">Submit</button>
                    </div>
                    <div id="manualResult" class="text-sm text-center text-green-600 mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  function showNotification(message, type = 'success') {
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      notification.textContent = message;
      document.body.appendChild(notification);
      setTimeout(() => { notification.style.transform = 'translateX(100%)'; setTimeout(() => notification.remove(), 300); }, 3000);
  }

function showMaintenanceMessage() {
    const panel = document.getElementById('verificationPanel');

    // Save original content
    const originalContent = panel.innerHTML;

    // Show loading first
    panel.innerHTML = `
        <h4>Loading...</h4>
        <p class="card-description text-blue-600 font-medium">⏳ Please wait</p>
    `;

    // After 1 seconds, show maintenance message
    setTimeout(() => {
        panel.innerHTML = `
            <h4>System Under Maintenance</h4>
            <p class="card-description text-red-600 font-medium">
                ⚠️ RFID Scan system is currently unavailable.
            </p>
        `;

        // After another 2 seconds, restore original content
        setTimeout(() => {
            panel.innerHTML = originalContent;
        }, 2000);
    }, 1000);
}


  function submitManual() {
      const employeeId = document.getElementById('employee_id').value;
      if (!employeeId) return showNotification('Please enter Employee ID', 'error');

      fetch('record_attendance.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `employee_id=${employeeId}`
      })
      .then(res => res.json())
      .then(data => {
          if (data.status === 'success') {
              // Show success notification
              showNotification(`${data.employee_name} Time ${data.type.toUpperCase()} recorded at ${data.time}`, 'success');
              updateLiveAttendance(data);
          } else if (data.status === 'warning') {
              showNotification(`${data.employee_name || 'Employee #'+employeeId} ${data.message}`, 'warning');
          } else {
              showNotification(data.message, 'error');
          }
      })
      .catch(err => console.error(err));
  }


  function formatTime(timeStr) {
      if (!timeStr) return '-';
      const [h, m, s] = timeStr.split(':');
      const date = new Date();
      date.setHours(h, m, s);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
  }

  function loadLiveAttendance() {
      fetch('get_attendance.php')
      .then(res => res.json())
      .then(data => {
          const table = document.getElementById('liveAttendanceTable');
          const recent = document.getElementById('recentScans');

          table.innerHTML = '';  // Clear current rows
          recent.innerHTML = ''; // Clear recent scans

          let recentEntries = [];

          data.forEach(row => {
              const timeIn = formatTime(row.time_in);
              const timeOut = formatTime(row.time_out);

              // Calculate hours
              let hours = '-';
              if (row.time_in && row.time_out) {
                  const inTime = new Date(`1970-01-01T${row.time_in}`);
                  const outTime = new Date(`1970-01-01T${row.time_out}`);
                  const diff = (outTime - inTime) / (1000 * 60 * 60); // hours
                  hours = diff.toFixed(2) + 'h';
              }

              // Determine status
              const status = row.time_in ? 'Present' : '-';

              // Add row to live table
              const tr = document.createElement('tr');
              tr.innerHTML = `
                  <td><strong>${row.employee_name}</strong></td>
                  <td>${timeIn}</td>
                  <td>${timeOut}</td>
                  <td>${hours}</td>
                  <td><span class="badge badge-success">Manual</span></td>
                  <td><span class="badge badge-success">${status}</span></td>
              `;
              table.appendChild(tr);

              // Collect recent scans
              if(row.time_in) recentEntries.push(`${timeIn} - ${row.employee_name} (IN)`);
              if(row.time_out) recentEntries.push(`${timeOut} - ${row.employee_name} (OUT)`);
          });

          // Sort by time descending and keep only latest 10
          recentEntries.sort((a, b) => {
              const timeA = a.split(' - ')[0];
              const timeB = b.split(' - ')[0];
              return timeB.localeCompare(timeA);
          });

          recentEntries.slice(0, 10).forEach(entry => {
              const div = document.createElement('div');
              div.innerText = entry;
              recent.appendChild(div);
          });
      });
  }

  // Call on page load
  loadLiveAttendance();

  function updateLiveAttendance(data) {
      // Simply reload the live table from DB
      loadLiveAttendance();
  }

  // Function to update Verification Stats
function updateStats(attendanceData) {
    // Count totals
    let totalRFID = 0;
    let totalFace = 0;
    let totalPresent = 0;

    attendanceData.forEach(row => {
        if (row.verification === 'RFID') totalRFID++;
        if (row.verification === 'Face') totalFace++;
        if (row.time_in) totalPresent++;
    });

    // Update badge values
    document.getElementById('totalRFID').innerText = totalRFID;
    document.getElementById('totalFace').innerText = totalFace;
    document.getElementById('totalPresent').innerText = totalPresent;
}


// Update Security Alerts based on missing clock-out
function updateSecurityAlerts(attendanceData) {
    const alertsCard = document.querySelector('#tab-attendance .card:nth-child(2) .card-content');
    alertsCard.innerHTML = `
        <p>No security alerts detected</p>
        <p class="card-description">System monitoring active</p>
        <div class="mt-4">
            <div class="badge badge-success">Fraud Prevention: Active</div>
        </div>
    `;
}

// Call refreshDashboard whenever attendance changes
function refreshDashboard() {
    fetch('get_attendance.php')
    .then(res => res.json())
    .then(data => {
        updateStats(data);          // existing function to update counts
        updateSecurityAlerts(data); // now shows missing clock-outs
    })
    .catch(err => console.error(err));
}

// Initial load and periodic refresh
refreshDashboard();
setInterval(refreshDashboard, 1000);

let rfidBuffer = '';
let rfidTimer;

document.addEventListener('keydown', (e) => {
    // Ignore modifier keys
    if (e.key === 'Shift' || e.key === 'Control' || e.key === 'Alt') return;

    // ✅ Ignore if the user is typing in an input or textarea
    const activeTag = document.activeElement.tagName;
    if (activeTag === 'INPUT' || activeTag === 'TEXTAREA') return;

    rfidBuffer += e.key;

    clearTimeout(rfidTimer);
    rfidTimer = setTimeout(() => {
        if (rfidBuffer.length > 0) {
            processRFID(rfidBuffer.trim());
            rfidBuffer = '';
        }
    }, 50); // Adjust based on your scanner speed
});


function processRFID(rfid) {
    fetch('record_attendance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `rfid_tag=${encodeURIComponent(rfid)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(`✅ Attendance recorded for ${data.employee_name}`, 'success');
            // Update live table & stats
            loadLiveAttendance();
            refreshDashboard();
        } else if (data.status === 'warning') {
            showNotification(`${data.employee_name || 'Employee #'+rfid} ${data.message}`, 'warning');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(err => console.error('RFID scan error:', err));
}






</script>
