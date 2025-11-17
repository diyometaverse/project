        <div class="sidebar">
            <div class="mb-6">
                <h3 class="gradient-text">🏗️ MEPFS PayrollPro</h3>
                <!-- <p class="card-description">Research System v1.0</p>
                <div class="mt-2">
                    <p style="font-size: 0.75rem; color: var(--success);" class="live-time" id="currentTime"></p>
                </div> -->
                <br>
                <br>
            </div>
            
            <nav class="space-y-2">
                <button class="nav-item active" data-tab="dashboard">
                    <span class="icon">📊</span>
                    <span>Dashboard Overview</span>
                </button>
                <button class="nav-item" data-tab="employees">
                    <span class="icon">👥</span>
                    <span>Employee Management</span>
                </button>
                <button class="nav-item" data-tab="attendance">
                    <span class="icon">⏰</span>
                    <span>Attendance Tracking</span>
                </button>
                <button class="nav-item" data-tab="timesheets">
                    <span class="icon">📋</span>
                    <span>Timesheet Processing</span>
                </button>
                
                <div style="margin: 1rem 0; font-size: 0.75rem; font-weight: var(--font-weight-medium); color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em;">
                    Analytics
                </div>
                
                <button class="nav-item" data-tab="payroll">
                    <span class="icon">💰</span>
                    <span>Payroll Processing</span>
                </button>
                <button class="nav-item" data-tab="reports">
                    <span class="icon">📊</span>
                    <span>Reports & Security</span>
                </button>
                <!-- <button class="nav-item" data-tab="forecasting">
                    <span class="icon">📈</span>
                    <span>Payroll Forecasting</span>
                </button> -->
            </nav>

            <!-- System Status -->
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <h4 style="font-size: 0.75rem; font-weight: var(--font-weight-medium); color: var(--muted-foreground); margin-bottom: 0.5rem; text-transform: uppercase;">System Status</h4>
                <div class="module-operational">
                    <span style="font-size: 0.75rem;">All Systems</span>
                    <span class="badge badge-success">✓</span>
                </div>
            </div>

            <div style="margin-top: auto; padding-top: 2rem;">
                <button id="logoutBtn" class="btn btn-ghost w-full">
                    <span class="icon">↩️</span>
                    <span class="ml-2">Logout</span>
                </button>
            </div>
        </div>
<script>
document.getElementById("logoutBtn").addEventListener("click", function () {
    // Optional confirmation
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "logout.php";
    }
});
</script>
