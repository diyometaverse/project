<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all employees
$result = $conn->query("SELECT employee_id, first_name, last_name, department, position, face_image_path FROM employees ORDER BY first_name ASC");
if (!$result) die("Employee query failed: " . $conn->error);

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

// Today's attendance
$today = date("Y-m-d");
$sql_attendance = "SELECT * FROM attendance WHERE date='$today'";
$attendance_result = $conn->query($sql_attendance);
if (!$attendance_result) die("Attendance query failed: " . $conn->error);

$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_data[$row['employee_id']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Management</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; margin:0; padding:20px; padding-top:70px;}
.table { width:100%; border-collapse: collapse; margin-top:10px; background:#fff;}
.table th, .table td { border:1px solid #ddd; padding:8px; text-align:center; }
.table th { background: #f0f0f0; position: sticky; top: 0; }
.btn { padding:5px 10px; border-radius:5px; cursor:pointer; margin:2px; font-weight:600; }
.btn-success { background-color:#16a34a; color:white; border:none; }
.btn-warning { background-color:#f59e0b; color:white; border:none; }
.btn-primary { background-color:#2563eb; color:white; border:none; }
.card { background:#fff; padding:15px; border-radius:10px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.stats-card .stats-content { display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; }
.stat-item { background:#f9f9f9; padding:10px; border-radius:8px; text-align:center; }
.badge { padding:3px 8px; border-radius:4px; font-size:0.9rem; }
.badge-success { background:#16a34a; color:#fff; }
.badge-primary { background:#2563eb; color:#fff; }
.badge-info { background:#0ea5e9; color:#fff; }
#recentScans { max-height: 200px; overflow-y:auto; }
#recentScans div { padding:5px 10px; border-bottom:1px solid #eee; }
#cameraContainer { text-align:center; display:none; margin-top:10px; }

#cameraBar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #111;
    padding: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 999;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    display: none;
}
#cameraBar video {
    width: 120px;
    height: 90px;
    border-radius: 6px;
    object-fit: cover;
    border: 2px solid #fff;
}
#cameraBar button {
    background: #ef4444;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
</style>
<script async src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>

<!-- Camera Bar -->
<div id="cameraBar">
    <video id="cameraBarVideo" autoplay playsinline muted></video>
    <button class="btn btn-warning" onclick="stopCamera()">✖ Close</button>
</div>

<div id="tab-attendance">
    <h1>Attendance Management</h1>
    <p class="card-description">RFID + Biometric Face Verification System</p>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">

        <!-- Left Column: Attendance Table -->
        <div class="card">
            <h3>Live Attendance Tracking</h3>
            <button class="btn btn-primary" onclick="showMaintenanceMessage()">📱Scan RFID</button>
            <table class="table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($employees as $emp):
                        $att = $attendance_data[$emp['employee_id']] ?? null;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['first_name'].' '.$emp['last_name']) ?></td>
                        <td><?= htmlspecialchars($emp['department']) ?></td>
                        <td id="timein-<?= $emp['employee_id'] ?>"><?= $att['time_in'] ?? '-' ?></td>
                        <td id="timeout-<?= $emp['employee_id'] ?>"><?= $att['time_out'] ?? '-' ?></td>
                        <td id="status-<?= $emp['employee_id'] ?>"><?= ucfirst($att['status'] ?? 'absent') ?></td>
                        <td>
                            <?php if(!$att || !$att['time_in']): ?>
                                <button class="btn btn-success" onclick="clockIn(<?= $emp['employee_id'] ?>)">Time In</button>
                                <button class="btn btn-primary" onclick="verifyFace(<?= $emp['employee_id'] ?>)">Face Verify</button>
                            <?php endif; ?>
                            <?php if($att && !$att['time_out']): ?>
                                <button class="btn btn-warning" onclick="clockOut(<?= $emp['employee_id'] ?>)">Time Out</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Stats -->
            <div class="card stats-card">
                <h3>Verification Stats</h3>
                <div class="stats-content" id="stats">
                    <div class="stat-item">
                        <div>Total RFID</div>
                        <div><span class="badge badge-info" id="totalRFID">0</span></div>
                    </div>
                    <div class="stat-item">
                        <div>Total Face</div>
                        <div><span class="badge badge-primary" id="totalFace">0</span></div>
                    </div>
                    <div class="stat-item">
                        <div>Total Present</div>
                        <div><span class="badge badge-primary" id="totalPresent">0</span></div>
                    </div>
                </div>
            </div>

            <!-- Security Alerts -->
            <div class="card">
                <h3>Security Alerts</h3>
                <div>No security alerts detected</div>
                <div class="mt-2"><span class="badge badge-success">Fraud Prevention: Active</span></div>
            </div>

            <!-- Recent Scans -->
            <div class="card">
                <h3>Recent Scans</h3>
                <div id="recentScans"></div>
            </div>

            <!-- Manual Entry -->
            <div class="card">
                <h3>Manual Attendance Entry</h3>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="number" id="employee_id" placeholder="Enter Employee ID" style="flex:1;">
                    <button class="btn btn-primary" onclick="submitManual()">Submit</button>
                </div>
                <div id="manualResult" style="text-align:center; margin-top:10px; color:green;"></div>
            </div>
        </div>
    </div>

    <!-- Camera for Face Verification -->
    <div id="cameraContainer">
        <video id="liveCamera" autoplay playsinline style="width:100%; max-width:400px;"></video>
    </div>
</div>

<script>
// --- Clock In/Out Functions ---
function clockIn(empId){
    fetch('attendance_mark.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({employee_id:empId, action:'time_in', date:'<?= $today ?>'})
    }).then(res=>res.json()).then(data=>{
        if(data.success){
            document.getElementById('timein-'+empId).textContent = data.time_in;
            document.getElementById('status-'+empId).textContent = 'Present';
            refreshStats();
        } else { alert(data.message); }
    });
}

function clockOut(empId){
    fetch('attendance_mark.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({employee_id:empId, action:'time_out', date:'<?= $today ?>'})
    }).then(res=>res.json()).then(data=>{
        if(data.success){
            document.getElementById('timeout-'+empId).textContent = data.time_out;
        } else { alert(data.message); }
    });
}

// --- Face Verification ---
let cameraStream = null;

function startCameraBar() {
    const bar = document.getElementById('cameraBar');
    const video = document.getElementById('cameraBarVideo');

    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        cameraStream = stream;
        video.srcObject = stream;
        bar.style.display = 'flex';
        video.play();
    })
    .catch(err => console.error('Camera error:', err));
}

function stopCamera() {
    const bar = document.getElementById('cameraBar');
    const video = document.getElementById('cameraBarVideo');
    if(cameraStream){
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    video.srcObject = null;
    bar.style.display = 'none';
}

function verifyFace(empId){
    startCameraBar(); // Show live camera bar
    document.getElementById('cameraContainer').style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video:true }).then(stream=>{
        const video = document.getElementById('liveCamera');
        video.srcObject = stream;
        video.play();
        setTimeout(()=>{ captureFace(empId); },3000);
    }).catch(err=>console.error(err));
}

function captureFace(empId){
    const video = document.getElementById('liveCamera');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video,0,0,canvas.width,canvas.height);
    const face_image = canvas.toDataURL('image/jpeg');

    fetch('record_attendance_face.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`employee_id=${empId}&face_image=${encodeURIComponent(face_image)}&verification=Face`
    }).then(res=>res.json()).then(data=>{
        if(data.success){ alert('Face Verified and Attendance Recorded'); refreshStats(); location.reload(); }
        else{ alert(data.message); }
    });
}

// --- Manual Entry ---
function submitManual(){
    const empId = document.getElementById('employee_id').value;
    if(!empId) return alert('Enter Employee ID');
    fetch('record_attendance.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`employee_id=${empId}`
    }).then(res=>res.json()).then(data=>{
        document.getElementById('manualResult').textContent = data.message;
        refreshStats();
        location.reload();
    });
}

// --- Dummy Stats Update ---
function refreshStats(){
    const rows = document.querySelectorAll('.table tbody tr');
    let totalPresent = 0, totalFace = 0, totalRFID = 0;
    rows.forEach(r=>{
        const status = r.querySelector('td:nth-child(5)').textContent;
        if(status==='Present') totalPresent++;
    });
    document.getElementById('totalPresent').textContent = totalPresent;
}
</script>

</body>
</html>
