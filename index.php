<?php
require_once "db.php"; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php if(isset($_GET['success'])): ?>
<script>
alert('Employee added successfully!');
</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEPFS PayrollPro - Comprehensive Payroll Management System</title>
    <style>
        /* CSS Variables from globals.css */
        :root {
            --font-size: 14px;
            --background: #ffffff;
            --foreground: oklch(0.145 0 0);
            --card: #ffffff;
            --card-foreground: oklch(0.145 0 0);
            --popover: oklch(1 0 0);
            --popover-foreground: oklch(0.145 0 0);
            --primary: #030213;
            --primary-foreground: oklch(1 0 0);
            --secondary: oklch(0.95 0.0058 264.53);
            --secondary-foreground: #030213;
            --muted: #ececf0;
            --muted-foreground: #717182;
            --accent: #e9ebef;
            --accent-foreground: #030213;
            --destructive: #d4183d;
            --destructive-foreground: #ffffff;
            --border: rgba(0, 0, 0, 0.1);
            --input: transparent;
            --input-background: #f3f3f5;
            --switch-background: #cbced4;
            --font-weight-medium: 500;
            --font-weight-normal: 400;
            --ring: oklch(0.708 0 0);
            --radius: 0.625rem;
            --success: #22c55e;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: var(--font-size);
        }

        body {
            background-color: var(--background);
            color: var(--foreground);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
        }

        /* Typography matching globals.css */
        h1 { font-size: 2rem; font-weight: var(--font-weight-medium); line-height: 1.5; }
        h2 { font-size: 1.5rem; font-weight: var(--font-weight-medium); line-height: 1.5; }
        h3 { font-size: 1.25rem; font-weight: var(--font-weight-medium); line-height: 1.5; }
        h4 { font-size: 1rem; font-weight: var(--font-weight-medium); line-height: 1.5; }
        p { font-size: 1rem; font-weight: var(--font-weight-normal); line-height: 1.5; }
        label { font-size: 1rem; font-weight: var(--font-weight-medium); line-height: 1.5; }
        button { font-size: 1rem; font-weight: var(--font-weight-medium); line-height: 1.5; }
        input { font-size: 1rem; font-weight: var(--font-weight-normal); line-height: 1.5; }

        /* Layout */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .space-y-1 > * + * { margin-top: 0.25rem; }
        .space-y-2 > * + * { margin-top: 0.5rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }

        /* Components */
        .card {
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .card-header { padding: 1.5rem 1.5rem 0; }
        .card-content { padding: 1.5rem; }
        .card-title { font-size: 1.25rem; font-weight: var(--font-weight-medium); margin-bottom: 0.5rem; }
        .card-description { color: var(--muted-foreground); font-size: 0.875rem; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: var(--font-weight-medium);
            padding: 0.5rem 1rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--primary-foreground);
        }

        .btn-primary:hover { opacity: 0.9; }
        
        .btn-secondary {
            background: var(--secondary);
            color: var(--secondary-foreground);
        }

        .btn-ghost {
            background: transparent;
            color: var(--foreground);
        }

        .btn-ghost:hover { background: var(--accent); }

        .btn-gradient {
            background: linear-gradient(to right, #2563eb, #059669);
            color: white;
            height: 2.75rem;
        }

        .btn-gradient:hover {
            background: linear-gradient(to right, #1d4ed8, #047857);
        }

        .input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--input-background);
            font-size: 0.875rem;
            height: 2.75rem;
        }

        .input:focus {
            outline: 2px solid var(--ring);
            outline-offset: 2px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: var(--font-weight-medium);
        }

        .badge-default { background: var(--primary); color: var(--primary-foreground); }
        .badge-secondary { background: var(--secondary); color: var(--secondary-foreground); }
        .badge-destructive { background: var(--destructive); color: var(--destructive-foreground); }
        .badge-success { background: var(--success); color: white; }
        .badge-warning { background: var(--warning); color: white; }

        /* Login Screen Specific */
        .login-container {
            min-height: 100vh;
            background: linear-gradient(to bottom right, #dbeafe, #ffffff, #dcfce7);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            width: 100%;
            max-width: 28rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-logo {
            background: linear-gradient(to bottom right, #2563eb, #059669);
            padding: 0.75rem;
            border-radius: 0.5rem;
            display: inline-block;
            margin-right: 0.75rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted-foreground);
            padding: 0.25rem;
        }

        .password-toggle:hover { color: var(--foreground); }

        .security-notice {
            background: #dbeafe;
            border: 1px solid #93c5fd;
        }

        .footer-icons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--muted-foreground);
        }

        /* Dashboard Layout */
        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: var(--card);
            border-right: 1px solid var(--border);
            padding: 1rem;
            overflow-y: auto;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .nav-item {
            width: 100%;
            padding: 0.5rem 1rem;
            text-align: left;
            border: none;
            background: none;
            border-radius: var(--radius);
            cursor: pointer;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--foreground);
        }

        .nav-item:hover { background: var(--accent); }
        .nav-item.active { background: var(--primary); color: var(--primary-foreground); }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card);
            padding: 1.5rem;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }

        .stat-description {
            color: var(--muted-foreground);
            font-size: 0.875rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            font-weight: var(--font-weight-medium);
            background: var(--muted);
        }

        .progress {
            width: 100%;
            height: 0.5rem;
            background: var(--muted);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .tabs { width: 100%; }

        .tab-list {
            display: flex;
            background: var(--muted);
            border-radius: var(--radius);
            padding: 0.25rem;
            margin-bottom: 1rem;
        }

        .tab-trigger {
            flex: 1;
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            border-radius: calc(var(--radius) - 2px);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: var(--font-weight-medium);
        }

        .tab-trigger.active {
            background: var(--background);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .hidden { display: none !important; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-left: left; }

        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .ml-2 { margin-left: 0.5rem; }
        .mr-2 { margin-right: 0.5rem; }
        .mr-3 { margin-right: 0.75rem; }

        .w-full { width: 100%; }
        .h-4 { height: 1rem; }
        .w-4 { width: 1rem; }
        .h-11 { height: 2.75rem; }

        .relative { position: relative; }
        .pr-10 { padding-right: 2.5rem; }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .loading-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid white;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Real-time updates */
        .live-time {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--success);
        }

        .system-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: var(--radius);
            margin-bottom: 0.5rem;
        }

        .status-operational { background: #dcfce7; color: #166534; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-error { background: #fee2e2; color: #991b1b; }

        /* Enhanced verification panels */
        .verification-panel {
            border: 2px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }

        .scanning-active {
            border-color: var(--info);
            background: linear-gradient(135deg, #dbeafe, #e0f2fe);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard { flex-direction: column; }
            .sidebar { width: 100%; height: auto; border-right: none; border-bottom: 1px solid var(--border); }
            .main-content { padding: 1rem; }
            .grid-cols-2, .grid-cols-3, .grid-cols-4 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }

        /* Icons */
        .icon {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            text-align: center;
            font-size: 0.875rem;
        }

        .icon-lg {
            font-size: 2rem;
        }

        .icon-xl {
            font-size: 3rem;
        }

        /* Enhanced gradients and effects */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #2563eb, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-highlight {
            background: linear-gradient(135deg, #f0f9ff, #ecfdf5);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
            margin: 0.5rem 0;
        }

        /* Enhanced notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .notification.success { background: var(--success); }
        .notification.error { background: var(--destructive); }
        .notification.warning { background: var(--warning); }
        .notification.info { background: var(--info); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Module status indicators */
        .module-status {
            padding: 0.5rem;
            border-radius: var(--radius);
            margin: 0.25rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .module-operational {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid var(--success);
        }

        .module-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid var(--warning);
        }

        .module-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--destructive);
        }

        /* Activity feed */
        .activity-item {
            display: flex;
            align-items: start;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: var(--radius);
            margin-bottom: 0.75rem;
            background: #fafafa;
            border-left: 3px solid var(--border);
        }

        .activity-success { border-left-color: var(--success); }
        .activity-warning { border-left-color: var(--warning); }
        .activity-info { border-left-color: var(--info); }
        .activity-error { border-left-color: var(--destructive); }
    </style>
</head>
<body>
    <!-- Dashboard -->
    <div id="dashboard" class="dashboard">
        <!-- Sidebar -->
         <?php include "sidebar.php"?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard Overview Tab -->
             <?php include "dashboard.php"?>

            <!-- Employee Management Tab -->
             <?php include "employee.php"?>

            <!-- Enhanced Attendance Tracking Tab -->
             <?php include "employees.php"?>

            <!-- JavaScript for Manual Attendance -->

            <!-- Enhanced Timesheet Processing Tab -->
             <?php include "timesheet.php"?>

            <!-- Enhanced Payroll Processing Tab -->
             <?php include "payroll.php"?>

            <!-- Enhanced Reports & Security Tab -->
             <?php include "report.php"?>

            <!-- Enhanced Payroll Forecasting Tab -->

            <!-- Enhanced Research Compliance Tab -->
        </div>
    </div>

<script>
     // ----------------- State & Variables -----------------
    let currentTab = 'dashboard';
    let currentSubtab = 'reports';
    let systemStartTime = new Date();

    // ----------------- Tab Navigation -----------------
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

    const selectedTab = document.getElementById(`tab-${tabName}`);
    if (selectedTab) selectedTab.classList.add('active');

    const selectedNavItem = document.querySelector(`[data-tab="${tabName}"]`);
    if (selectedNavItem) selectedNavItem.classList.add('active');

    currentTab = tabName;

    // 👇 Auto-select first subtab if the tab has one
    if (tabName === 'reports') {
        switchSubtab('reports');  // auto show reports subtab
    }
}

    // ----------------- Subtab Navigation -----------------
    function switchSubtab(subtabName) {
        document.querySelectorAll('[id^="subtab-"]').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('[data-subtab]').forEach(trigger => trigger.classList.remove('active'));

        const selectedSubtab = document.getElementById(`subtab-${subtabName}`);
        if (selectedSubtab) selectedSubtab.classList.add('active');

        const selectedTrigger = document.querySelector(`[data-subtab="${subtabName}"]`);
        if (selectedTrigger) selectedTrigger.classList.add('active');

        currentSubtab = subtabName;
    }

    // ----------------- Real-time Updates -----------------
    function updateRealTimeData() {
        const now = new Date();
        const timeElements = document.querySelectorAll('#currentTime, #dashboardTime');
        timeElements.forEach(el => { if (el) el.textContent = now.toLocaleTimeString(); });

        const lastSyncElement = document.getElementById('lastSync');
        if (lastSyncElement) {
            const lastSyncMinutes = Math.floor((now - systemStartTime) / 60000);
            lastSyncElement.textContent = `${lastSyncMinutes} min ago`;
        }
    }
    setInterval(updateRealTimeData, 1000);

    // ----------------- RFID Scan Simulation -----------------
    function simulateRFIDScan() {
        const verificationPanel = document.getElementById('verificationPanel');
        const scanBtn = document.getElementById('scanRFID');

        verificationPanel.classList.add('scanning-active');
        verificationPanel.innerHTML = `
            <div class="text-center">
                <div class="loading-spinner" style="margin: 0 auto 1rem; width: 3rem; height: 3rem; border: 3px solid var(--info); border-top: 3px solid transparent;"></div>
                <h4>Scanning RFID Card...</h4>
                <p class="card-description">Please hold card near scanner</p>
            </div>
        `;
        scanBtn.disabled = true;
        scanBtn.textContent = 'Scanning...';

        setTimeout(() => {
            verificationPanel.classList.remove('scanning-active');
            verificationPanel.innerHTML = `
                <div class="text-center">
                    <div class="icon icon-xl mb-4" style="color: var(--success);">✅</div>
                    <h4>RFID Verified Successfully</h4>
                    <p class="card-description">RC001 - Christel Arpon</p>
                    <div class="mt-4">
                        <span class="badge badge-success">RFID Success</span>
                        <span class="badge badge-success ml-2">Face Recognition: 94%</span>
                    </div>
                </div>
            `;
            scanBtn.disabled = false;
            scanBtn.innerHTML = '<span class="icon">📱</span><span class="ml-2">Scan RFID</span>';
            showNotification('RFID scan successful - Welcome Christel!', 'success');
        }, 2000);
    }

    // ----------------- Notifications -----------------
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => { notification.style.transform = 'translateX(100%)'; setTimeout(() => notification.remove(), 300); }, 3000);
    }

    // ----------------- Event Listeners -----------------
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => switchTab(item.getAttribute('data-tab')));
        });

        // Subtab navigation
        document.querySelectorAll('[data-subtab]').forEach(trigger => {
            trigger.addEventListener('click', () => switchSubtab(trigger.getAttribute('data-subtab')));
        });

        // RFID scan button
        const scanBtn = document.getElementById('scanRFID');
        if (scanBtn) scanBtn.addEventListener('click', simulateRFIDScan);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                switch(e.key) {
                    case '1': switchTab('dashboard'); break;
                    case '2': switchTab('employees'); break;
                    case '3': switchTab('attendance'); break;
                    case '4': switchTab('payroll'); break;
                    case '5': switchTab('reports'); break;
                    case '6': switchTab('research'); break;
                }
            }
        });
    });

    // ----------------- Currency Formatting -----------------
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0 }).format(amount);
    }

    function formatCurrencyCompact(amount) {
        if (amount >= 1000000) return '₱' + (amount / 1000000).toFixed(1) + 'M';
        if (amount >= 1000) return '₱' + (amount / 1000).toFixed(0) + 'K';
        return '₱' + amount.toLocaleString();
    }

    console.log('🏗️ Dashboard JS loaded - Django authentication ready');


</script>
</script>

</body>
</html>