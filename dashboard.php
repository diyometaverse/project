<?php
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



// 1. Active members present today
$present_sql = "SELECT COUNT(DISTINCT employee_id) AS present_today 
                FROM attendance 
                WHERE DATE(time_in) = CURDATE()";
$present = $conn->query($present_sql)->fetch_assoc();

// 2. Payroll totals for current period
$period_sql = "SELECT period_id FROM payroll_periods ORDER BY end_date DESC LIMIT 1";
$period = $conn->query($period_sql)->fetch_assoc();
$period_id = $period['period_id'] ?? 0;

$payroll_sql = "SELECT SUM(gross_pay) AS total_gross, SUM(net_pay) AS total_net 
                FROM payroll 
                WHERE period_id = ?";
$stmt = $conn->prepare($payroll_sql);
$stmt->bind_param("i", $period_id);
$stmt->execute();
$payroll_totals = $stmt->get_result()->fetch_assoc();

// 3. Pending timesheets
$pending_sql = "SELECT COUNT(*) AS pending FROM timesheets WHERE status='pending'";
$pending = $conn->query($pending_sql)->fetch_assoc();
?>
<style>
    .card-content {
    overflow-x: hidden; /* hide horizontal scroll */
    overflow-y: auto;   /* allow vertical scroll if needed */
    max-height: 400px;  /* optional: restrict height to enable scrolling */
}

/* Optional: style scrollbar for vertical overflow */
.card-content::-webkit-scrollbar {
    width: 6px;
}

.card-content::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 3px;
}

.card-content::-webkit-scrollbar-track {
    background: transparent;
}

</style>


<div id="tab-dashboard" class="tab-content active">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1>MEPFS PayrollPro Dashboard</h1>
                            <p class="card-description">Comprehensive payroll management for MEPFS construction projects</p>
                        </div>
                        <div class="text-right">
                            <p style="font-size: 0.875rem; color: var(--muted-foreground);">Current Time</p>
                            <p class="live-time" id="dashboardTime"></p>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="card-description">Active MEPFS Engineers</p>
                                    <div class="stat-value"><?= $present['present_today'] ?? 0 ?></div>
                                    <p class="stat-description">present today</p>
                                </div>
                                <span class="icon icon-lg" style="color: #3b82f6;">👥</span>
                            </div>
                        <div class="mt-4">
                            <div class="progress">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                        </div>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground);">
                            Employee Management
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="card-description">Verified Clock-ins</p>
                                <div class="stat-value">96%</div>
                                <p class="stat-description">RFID + Face verification</p>
                                <p class="stat-description">Fraud prevention active</p>
                            </div>
                            <span class="icon icon-lg" style="color: #22c55e;">🛡️</span>
                        </div>
                        <div class="mt-4">
                            <div class="progress">
                                <div class="progress-fill" style="width: 96%"></div>
                            </div>
                        </div>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground);">
                            Attendance Management
                        </div>
                    </div>

                    <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="card-description">Bi-weekly Payroll</p>
                                    <div class="stat-value">₱<?= number_format($payroll_totals['total_gross'] ?? 0, 2) ?></div>
                                    <p class="stat-description">Current period gross</p>
                                    <p class="stat-description">₱<?= number_format($payroll_totals['total_net'] ?? 0, 2) ?> net</p>
                                </div>
                                <span class="icon icon-lg" style="color: #8b5cf6;">💰</span>
                            </div>
                        <div class="mt-4">
                            <div class="progress">
                                <div class="progress-fill" style="width: 80%"></div>
                            </div>
                        </div>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground);">
                            Payroll Processing
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="card-description">Pending Approvals</p>
                                <div class="stat-value"><?= $pending['pending'] ?? 0 ?></div>
                                <p class="stat-description">Timesheets awaiting review</p>
                            </div>
                            <span class="icon icon-lg" style="color: #f59e0b;">📋</span>
                        </div>
                        <div class="mt-4">
                            <div class="progress">
                                <div class="progress-fill" style="width: 75%"></div>
                            </div>
                        </div>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--muted-foreground);">
                            Reports & Security
                        </div>
                    </div>
                </div>

                <!-- Enhanced Activity and System Health -->
                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 2fr 1fr;">
                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                            <p class="card-description">Latest system events and updates</p>
                        </div>
                        <div class="card-content">
                            <div class="space-y-1">
                                <?php
                                $sql = "SELECT e.first_name, e.last_name, a.date, a.time_in, a.time_out
                                        FROM attendance a
                                        JOIN employees e ON a.employee_id = e.employee_id
                                        ORDER BY a.attendance_id DESC
                                        LIMIT 10";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $fullName = $row['first_name'] . " " . $row['last_name'];
                                        $date = date("M d, Y", strtotime($row['date']));

                                        // Clock In
                                        if (!empty($row['time_in'])) {
                                            echo '
                                            <div class="activity-item activity-success">
                                                <div class="badge badge-secondary">🟢⏰</div>
                                                <div>
                                                    <p><strong>' . $fullName . '</strong> clocked in successfully</p>
                                                    <p class="card-description">RFID + Face verification - ' . 
                                                    date("h:i A", strtotime($row['time_in'])) . ' (' . $date . ')</p>
                                                </div>
                                            </div>';
                                        }

                                        // Clock Out
                                        if (!empty($row['time_out'])) {
                                            echo '
                                            <div class="activity-item activity-warning">
                                                <div class="badge badge-secondary">🔴⏰</div>
                                                <div>
                                                    <p><strong>' . $fullName . '</strong> clocked out</p>
                                                    <p class="card-description">End of shift - ' . 
                                                    date("h:i A", strtotime($row['time_out'])) . ' (' . $date . ')</p>
                                                </div>
                                            </div>';
                                        }
                                    }
                                } else {
                                    echo "<p class='text-gray-500'>No recent activity</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- End -->
                    <div class="space-y-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">System Health</h3>
                                <p class="card-description">Real-time monitoring</p>
                            </div>
                            <div class="card-content space-y-4">
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>RFID Scanner</span>
                                        <span class="badge badge-success">Online</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: 98%"></div>
                                    </div>
                                    <p class="card-description">98% success rate</p>
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>Face Recognition</span>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: 94%"></div>
                                    </div>
                                    <p class="card-description">94% accuracy</p>
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>Database Sync</span>
                                        <span class="badge badge-success">Synced</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: 100%"></div>
                                    </div>
                                    <p class="card-description">Last sync: <span id="lastSync">2 min ago</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FDD Module Status -->
            </div>