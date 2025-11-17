<?php
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<style>

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

#liveCamera {
    transform: scaleX(-1);
}
</style>

<div id="tab-attendance" class="tab-content">
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
                        <button id="refreshAttendance" onclick="refreshAttendance()">Refresh Attendance</button>
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
    <div id="rfidResultPanel" class="mt-2 text-center"></div>
<div id="cameraContainer" style="display:none; margin-top:15px; text-align:center;">
    <video id="liveCamera" autoplay playsinline style="width:100%; max-width:400px;"></video>
</div>
</div>

<script async src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
// --- GLOBAL FUNCTIONS (needed for onclick buttons) ---
window.showMaintenanceMessage = function() {
    const panel = document.getElementById('verificationPanel');
    const originalContent = panel.innerHTML;
    panel.innerHTML = `
        <h4>Loading...</h4>
        <p class="card-description text-blue-600 font-medium">⏳ Please wait</p>
    `;
    setTimeout(() => {
        panel.innerHTML = `
            <h4>System Under Maintenance</h4>
            <p class="card-description text-red-600 font-medium">
                ⚠️ RFID Scan system is currently unavailable.
            </p>
        `;
        setTimeout(() => { panel.innerHTML = originalContent; }, 2000);
    }, 1000);
};

window.submitManual = function() {
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
            showNotification(`${data.employee_name} Time ${data.type.toUpperCase()} recorded at ${data.time}`, 'success');
            updateLiveAttendance(data);
        } else if (data.status === 'warning') {
            showNotification(`${data.employee_name || 'Employee #'+employeeId} ${data.message}`, 'warning');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(err => console.error(err));
};

window.startCameraTest = async function() {
    const video = document.getElementById("liveCamera");
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    video.play();

    // Continuously check for a face
    video.addEventListener("play", () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.body.append(canvas);
        
        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);
        
        setInterval(async () => {
            const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions());
            if (detections) {
                startCountdown();
            }
        }, 1000);
    });
}

function startCountdown() {
    let counter = 3;
    const interval = setInterval(() => {
        document.getElementById("countdown").innerText = counter;
        counter--;
        if (counter < 0) {
            clearInterval(interval);
            captureAndVerifyFace();
        }
    }, 1000);
}
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    loadLiveAttendance(); // Initial load
});

document.addEventListener("DOMContentLoaded", async () => {
    loadLiveAttendance(true);
    // --- DYNAMIC FACE-API.JS LOADING ---
    if (!window.faceapi) {
        await new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.src = "https://cdn.jsdelivr.net/npm/face-api.js";
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // --- FACE API MODELS LOADING ---
    let faceModelsLoaded = false;
    async function loadFaceModels() {
        if (faceModelsLoaded) return;
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/project/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/project/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/project/models');
            faceModelsLoaded = true;
        } catch (error) {
            console.error('Model loading error:', error);
            showNotification('Failed to load face models', 'error');
        }
    }

    // --- CAMERA MANAGEMENT ---
    let activeStream = null;

    async function loadCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === "videoinput");
            const selector = document.getElementById("cameraSelector");
            selector.innerHTML = "";
            videoDevices.forEach((device, index) => {
                const opt = document.createElement("option");
                opt.value = device.deviceId;
                opt.textContent = device.label || `Camera ${index + 1}`;
                selector.appendChild(opt);
            });
        } catch (error) {
            console.error("Camera list error:", error);
            showNotification("Unable to load camera list.", "error");
        }
    }

    navigator.mediaDevices.getUserMedia({ video: true }).finally(loadCameras);

    // --- NOTIFICATIONS ---
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // --- TIME FORMATTING ---
    function formatTime(timeStr) {
        if (!timeStr) return '-';
        const [h, m, s] = timeStr.split(':');
        const date = new Date();
        date.setHours(h, m, s);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    // --- LIVE ATTENDANCE ---
    function loadLiveAttendance(forceUpdate = false) {
        const storedData = JSON.parse(localStorage.getItem('attendanceData') || '{}');
        const lastUpdated = storedData.lastUpdated ? new Date(storedData.lastUpdated) : null;

        if (forceUpdate || !lastUpdated || (new Date() - lastUpdated) > 5 * 60 * 1000) {
            fetch('get_attendance.php')
                .then(res => res.json())
                .then(data => {
                    data.lastUpdated = new Date();
                    localStorage.setItem('attendanceData', JSON.stringify(data));
                    populateAttendanceTable(data);
                });
        } else {
            populateAttendanceTable(storedData);
        }
    }

    setInterval(() => { loadLiveAttendance(true); }, 60000);

    // Manual Refresh Function
    window.refreshAttendance = function() {
        loadLiveAttendance(true); // Force update from server
        showNotification('Attendance data refreshed!', 'success');
    }

    function populateAttendanceTable(data) {
        const table = document.getElementById('liveAttendanceTable');
        const recent = document.getElementById('recentScans');
        table.innerHTML = '';
        recent.innerHTML = '';
        let recentEntries = [];
        data.forEach(row => {
            const timeIn = formatTime(row.time_in);
            const timeOut = formatTime(row.time_out);
            let hours = '-';
            if (row.time_in && row.time_out) {
                const inTime = new Date(`1970-01-01T${row.time_in}`);
                const outTime = new Date(`1970-01-01T${row.time_out}`);
                hours = ((outTime - inTime) / (1000 * 60 * 60)).toFixed(2) + 'h';
            }
            const status = row.time_in ? 'Present' : '-';
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
            if (row.time_in) recentEntries.push(`${timeIn} - ${row.employee_name} (IN)`);
            if (row.time_out) recentEntries.push(`${timeOut} - ${row.employee_name} (OUT)`);
        });
        recentEntries.sort((a, b) => b.localeCompare(a));
        recentEntries.slice(0, 10).forEach(entry => {
            const div = document.createElement('div');
            div.innerText = entry;
            recent.appendChild(div);
        });
    }

    function updateLiveAttendance(data) {
        loadLiveAttendance();
    }

    // --- STATS & SECURITY ---
    function updateStats(attendanceData) {
        let totalRFID = 0, totalFace = 0, totalPresent = 0;
        attendanceData.forEach(row => {
            if (row.verification === 'RFID') totalRFID++;
            if (row.verification === 'Face') totalFace++;
            if (row.time_in) totalPresent++;
        });
        document.getElementById('totalRFID').innerText = totalRFID;
        document.getElementById('totalFace').innerText = totalFace;
        document.getElementById('totalPresent').innerText = totalPresent;
    }

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

    function refreshDashboard() {
        const data = JSON.parse(localStorage.getItem('attendanceData') || '[]');
        updateStats(data);
        updateSecurityAlerts(data);
    }

    setInterval(refreshDashboard, 500);

    // --- RFID BUFFER ---
    let rfidBuffer = '';
    let rfidTimer;

    document.addEventListener('keydown', (e) => {
        if (['Shift','Control','Alt'].includes(e.key)) return;
        if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
        rfidBuffer += e.key;
        clearTimeout(rfidTimer);
        rfidTimer = setTimeout(() => {
            if (rfidBuffer.length > 0) {
                processRFID(rfidBuffer.trim());
                rfidBuffer = '';
            }
        }, 50);
    });

    // --- RFID + FACE ATTENDANCE ---
    async function processRFID(rfid) {
        const res = await fetch('verify_rfid.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `rfid_tag=${encodeURIComponent(rfid)}`
        });
        const data = await res.json();
        if (!data.success) return showNotification(data.message, "error");

        const employee = data.employee;
        await loadFaceModels();
        await startCameraTest();

        // Show RFID Verified and countdown below camera controls
        document.getElementById("rfidResultPanel").innerHTML = `
            <h3>RFID Verified: ${employee.first_name} ${employee.last_name}</h3>
            <p class="card-description text-blue-600">Please stay still for <strong id="countdown">3</strong> seconds...</p>
        `;

        let counter = 3;
        const interval = setInterval(() => {
            counter--;
            document.getElementById("countdown").innerText = counter;
            if (counter === 0) {
                clearInterval(interval);
                captureAndVerifyFace(employee);
            }
        }, 1000);
    }

    async function captureAndVerifyFace(employee) {
        const video = document.getElementById("liveCamera");
        if (!video.videoWidth || !video.videoHeight) return showNotification("Camera not ready", "error");

        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);

        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 });

        const liveImage = await faceapi.detectSingleFace(canvas, options)
            .withFaceLandmarks()
            .withFaceDescriptor();
        if (!liveImage) return showNotification("Face not detected. Please try again.", "error");

        console.log("📸 RFID Employee:", employee.first_name, employee.last_name);
        console.log("📸 Stored Face Path:", employee.face_image_path);

        const storedImage = await faceapi.fetchImage(employee.face_image_path);
        const storedDescriptor = await faceapi.detectSingleFace(storedImage, options)
            .withFaceLandmarks()
            .withFaceDescriptor();
        if (!storedDescriptor) {
            console.error("❌ Stored face image invalid or no face detected in stored image");
            return showNotification("Stored face image invalid.", "error");
        }

        const distance = faceapi.euclideanDistance(liveImage.descriptor, storedDescriptor.descriptor);
        const MATCH_THRESHOLD = 0.45;
        
        console.log("🔍 Face Matching Results:");
        console.log("   Live Face Descriptor:", liveImage.descriptor);
        console.log("   Stored Face Descriptor:", storedDescriptor.descriptor);
        console.log("   Euclidean Distance:", distance);
        console.log("   Match Threshold:", MATCH_THRESHOLD);
        
        if (distance <= MATCH_THRESHOLD) {
            console.log("✅ MATCH! RFID verified. Face match confirmed.");
            finalizeAttendance(employee.employee_id, "Face");
        } else {
            console.error("❌ NO MATCH! Distance (" + distance + ") exceeds threshold (" + MATCH_THRESHOLD + ")");
            showNotification("Face mismatch detected!", "error");
        }
    }

    function finalizeAttendance(employee_id, verificationType) {
        // Capture the current frame from the video as base64
        const video = document.getElementById("liveCamera");
        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
        const face_image = canvas.toDataURL("image/jpeg"); // base64

        fetch('record_attendance_face.php', {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `employee_id=${employee_id}&face_image=${encodeURIComponent(face_image)}&verification=${verificationType}`
        })
        .then(res => res.json())
        .then(data => {
            console.log("PHP Debug Log:", data.debug);
            if (data.success) {
                showNotification("Attendance confirmed (Face Match).", "success");
                loadLiveAttendance();
                refreshDashboard();
                
                // Refresh the page after successful attendance
                location.reload();
            } else {
                showNotification(data.message, "error");
            }
        });
    }

    // --- INITIAL LOAD ---
    loadLiveAttendance();

    // --- CAMERA START FUNCTION FOR BUTTON ---
    window.startCameraTest = async function() {
        const video = document.getElementById("liveCamera");
        const container = document.getElementById("cameraContainer");
        const cameraId = document.getElementById("cameraSelector").value;
        try {
            if (activeStream) activeStream.getTracks().forEach(track => track.stop());
            const stream = await navigator.mediaDevices.getUserMedia({
                video: cameraId ? { deviceId: { exact: cameraId } } : true,
                audio: false
            });
            activeStream = stream;
            video.srcObject = stream;
            container.style.display = "block";
            showNotification("Camera feed active.", "success");
        } catch (error) {
            console.error("Camera Error:", error);
            showNotification("Unable to start the selected camera.", "error");
        }
    };

    // Force console output
    console.clear();
    console.log("✅ Script loaded - Console is active");
    window.addEventListener('error', (e) => console.error("Global Error:", e.error));
    window.addEventListener('unhandledrejection', (e) => console.error("Unhandled Promise:", e.reason));

    await startCameraTest();
});
</script>
