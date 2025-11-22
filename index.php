<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
<style>
        /* ============ DESIGN TOKENS ============ */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --neutral-50: #f9fafb;
            --neutral-100: #f3f4f6;
            --neutral-200: #e5e7eb;
            --neutral-300: #d1d5db;
            --neutral-600: #4b5563;
            --neutral-700: #374151;
            --neutral-900: #111827;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --transition: all 0.2s ease;
        }

        /* ============ GLOBAL STYLES ============ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                        'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                        sans-serif;
            background-color: var(--neutral-50);
            color: var(--neutral-900);
            line-height: 1.6;
        }

        /* ============ HEADER ============ */
        header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .header-left p {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        /* ============ CONTAINER ============ */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* ============ GRID LAYOUT ============ */
        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 1024px) {
            .grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* ============ CARDS ============ */
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background-color: var(--neutral-100);
            padding: 1.5rem;
            border-bottom: 1px solid var(--neutral-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--neutral-900);
            margin: 0;
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: var(--neutral-600);
            margin-top: 0.25rem;
        }

        .card-content {
            padding: 1.5rem;
        }

        /* ============ BUTTONS ============ */
        button {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        button:active {
            transform: translateY(0);
        }

        button:disabled {
            background-color: var(--neutral-300);
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background-color: var(--neutral-200);
            color: var(--neutral-900);
        }

        .btn-secondary:hover {
            background-color: var(--neutral-300);
        }

        /* ============ VERIFICATION PANEL ============ */
        #verificationPanel {
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0f4ff 100%);
            border: 2px dashed var(--primary-color);
            border-radius: var(--border-radius);
            padding: 3rem 2rem;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-dark);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #verificationPanel:hover {
            background: linear-gradient(135deg, #e0ebff 0%, #e8edff 100%);
            transform: scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        #verificationPanel h4 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--primary-dark);
        }

        #verificationPanel p {
            font-size: 0.95rem;
            color: var(--neutral-600);
            margin: 0;
        }

        .verification-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }

        /* ============ TABLE ============ */
        .table-wrapper {
            margin-top: 1.5rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        table thead {
            background-color: var(--neutral-100);
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--neutral-700);
            border-bottom: 2px solid var(--neutral-200);
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid var(--neutral-200);
            color: var(--neutral-600);
        }

        table tbody tr:hover {
            background-color: var(--neutral-50);
        }

        table strong {
            color: var(--neutral-900);
            font-weight: 600;
        }

        /* ============ BADGES ============ */
        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* ============ STATS GRID ============ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--neutral-100);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border: 1px solid var(--neutral-200);
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--neutral-600);
            font-weight: 500;
        }

        /* ============ TOAST NOTIFICATIONS ============ */
       #toastContainer {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            min-width: 280px;
            max-width: 400px;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }

        .toast.success {
            color: black;
            background: white;
            min-width: 1000px;
            max-width: 1000px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999; /* above backdrop */
            padding: 32px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        /* Backdrop for toast */
        .toast.success::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 9998;
        }

        .toast.error {
            background: var(--error-color);
        }

        .toast.warning {
            background: var(--warning-color);
        }

        .toast-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        /*  Added overlay backdrop for modal effect */
        .toast-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            animation: fadeIn 0.2s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /*  Improved toast styling with proper sizing and positioning */
        .toast {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .toast-message {
            font-size: 18px;
            font-weight: 600;
            color: #22c55e;
            text-align: center;
        }

        .toast-video {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            background: #000;
            object-fit: cover;
        }

        .toast-countdown {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            text-align: center;
        }

        .hide {
            display: none !important;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* ============ CAMERA CONTAINER ============ */
        #cameraContainer {
            display: none;
            margin-top: 1.5rem;
            text-align: center;
        }

        #liveCamera {
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            transform: scaleX(-1);
        }

        /* ============ RECENT SCANS ============ */
        #recentScans {
            max-height: 300px;
            overflow-y: auto;
        }

        #recentScans div {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--neutral-200);
            font-size: 0.9rem;
            color: var(--neutral-600);
        }

        #recentScans div:last-child {
            border-bottom: none;
        }

        #recentScans div:hover {
            background-color: var(--neutral-50);
        }

        /* ============ HIDDEN ELEMENTS ============ */
        #cameraSelector {
            display: none;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header-left h1 {
                font-size: 1.5rem;
            }

            .container {
                padding: 0 1rem;
            }

            table {
                font-size: 0.8rem;
            }

            table th, table td {
                padding: 0.75rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-container">
            <div class="header-left">
                <h1>🔐 Attendance Management</h1>
                <p>RFID + Biometric Face Verification System</p>
            </div>

        </div>
    </header>

    <!-- TOAST CONTAINER -->
    <div id="toastContainer"></div>

    <!-- MAIN CONTENT -->
    <div class="container">
        <div class="grid">
            <!-- LEFT COLUMN: MAIN TRACKING -->
            <div>
                <!-- LIVE TRACKING CARD -->
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">Live Attendance Tracking</h3>
                            <p class="card-subtitle">Real-time attendance with dual verification</p>
                        </div>
                        <button onclick="window.showMaintenanceMessage()">📱 Scan RFID</button>
                    </div>
                    <div class="card-content">
                        <!-- VERIFICATION PANEL -->
                        <div id="verificationPanel">
                            <div class="verification-icon">👤</div>
                            <h4>Ready for Verification</h4>
                            <p>Tap RFID card or click Scan RFID to begin</p>
                        </div>

                        <!-- TABLE -->
                        <div class="table-wrapper">
                            <table id="liveAttendanceTable">
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
                                <tbody>
                                    <!-- Populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: STATS & RECENT -->
            <div>
                <!-- STATS CARD -->
                
                <!-- RECENT SCANS CARD -->
                <div class="card" style="margin-top: 1.5rem;">
                    
                    <div id="cameraContainer">
                <video id="liveCamera" autoplay playsinline></video>
            </div>
    <select id="cameraSelector"></select>
                </div>


                <!-- RFID RESULT CARD -->
                <div class="card" id="rfidResultPanel" style="margin-top: 1.5rem;">
                    <!-- RFID result updates here -->
                </div>
            </div>
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
                });
        } else {
        }
    }

    setInterval(() => { loadLiveAttendance(true); }, 60000);

    // Manual Refresh Function
    window.refreshAttendance = function() {
        loadLiveAttendance(true); // Force update from server
        showNotification('Attendance data refreshed!', 'success');
    }

     function showToast(message, type = 'success', duration = 3000, refreshPage = false) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
            if (refreshPage) location.reload(); // Refresh page after toast disappears
        }, duration);

        return toast;
    }

    // --- STATS & SECURITY ---

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
            if (data.success) {
                showNotification("Attendance confirmed (Face Match).", "success");
                
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
<