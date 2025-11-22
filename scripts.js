function addManualRow() {
  const table = document
    .getElementById("manualAttendanceTable")
    .querySelector("tbody");
  const newRow = document.createElement("tr");

  newRow.innerHTML = `
        <td contenteditable="true">Enter Name</td>
        <td contenteditable="true">Enter Position</td>
        <td contenteditable="true">${new Date().toLocaleDateString()}</td>
        <td contenteditable="true">${new Date().toLocaleTimeString()}</td>
        <td>
            <button class="btn btn-sm btn-success" onclick="saveRow(this)">Save</button>
            <button class="btn btn-sm btn-danger" onclick="deleteRow(this)">Delete</button>
        </td>
    `;
  table.appendChild(newRow);
}

function saveRow(button) {
  const row = button.closest("tr");
  button.textContent = "Edit";
  button.classList.remove("btn-success");
  button.classList.add("btn-warning");
  button.setAttribute("onclick", "editRow(this)");
}

function editRow(button) {
  const row = button.closest("tr");
  row
    .querySelectorAll("td[contenteditable]")
    .forEach((cell) => cell.setAttribute("contenteditable", "true"));
  button.textContent = "Save";
  button.classList.remove("btn-warning");
  button.classList.add("btn-success");
  button.setAttribute("onclick", "saveRow(this)");
}

function deleteRow(button) {
  button.closest("tr").remove();
}

// Enhanced State Management
let isAuthenticated = false;
let currentTab = "dashboard";
let currentSubtab = "reports";
let systemStartTime = new Date();
let loginTime = null;

// Enhanced DOM Elements
const loginScreen = document.getElementById("loginScreen");
const dashboard = document.getElementById("dashboard");
const loginForm = document.getElementById("loginForm");
const logoutBtn = document.getElementById("logoutBtn");
const togglePassword = document.getElementById("togglePassword");
const eyeIcon = document.getElementById("eyeIcon");
const passwordField = document.getElementById("password");

// Enhanced Authentication
function login() {
  // This function is a placeholder for a real AJAX login.
  // In a real app, you would send the form data to the server.
  const loginBtn = document.getElementById("loginBtn");
  const loginText = document.getElementById("loginText");
  const loginSpinner = document.getElementById("loginSpinner");

  loginText.style.display = "none";
  loginSpinner.classList.remove("hidden");

  setTimeout(() => {
    // This simulates a successful login for the prototype.
    // A real app would handle the response from the server.
    isAuthenticated = true;
    loginTime = new Date();
    loginScreen.classList.add("hidden");
    dashboard.classList.remove("hidden");
    showNotification("Welcome to MEPFS PayrollPro Research System!", "success");
    updateRealTimeData();

    // Start real-time updates
    setInterval(updateRealTimeData, 1000);
  }, 1500);
}

function logout() {
  // In a real app, this would redirect to a Django logout URL.
  isAuthenticated = false;
  loginTime = null;
  loginScreen.classList.remove("hidden");
  dashboard.classList.add("hidden");

  // Reset login form
  document.getElementById("loginText").style.display = "inline";
  document.getElementById("loginSpinner").classList.add("hidden");

  showNotification("Logged out successfully", "info");
}

// Enhanced Tab Navigation
function switchTab(tabName) {
  document.querySelectorAll(".tab-content").forEach((tab) => {
    tab.classList.remove("active");
  });

  document.querySelectorAll(".nav-item").forEach((item) => {
    item.classList.remove("active");
  });

  const selectedTab = document.getElementById(`tab-${tabName}`);
  if (selectedTab) {
    selectedTab.classList.add("active");
  }

  const selectedNavItem = document.querySelector(`[data-tab="${tabName}"]`);
  if (selectedNavItem) {
    selectedNavItem.classList.add("active");
  }

  currentTab = tabName;
  showNotification(`Switched to ${tabName} module`, "info");
}

// Enhanced Subtab Navigation
function switchSubtab(subtabName) {
  document.querySelectorAll('[id^="subtab-"]').forEach((tab) => {
    tab.classList.remove("active");
  });

  document.querySelectorAll("[data-subtab]").forEach((trigger) => {
    trigger.classList.remove("active");
  });

  const selectedSubtab = document.getElementById(`subtab-${subtabName}`);
  if (selectedSubtab) {
    selectedSubtab.classList.add("active");
  }

  const selectedTrigger = document.querySelector(
    `[data-subtab="${subtabName}"]`
  );
  if (selectedTrigger) {
    selectedTrigger.classList.add("active");
  }

  currentSubtab = subtabName;
}

// Enhanced Real-time Updates
function updateRealTimeData() {
  const now = new Date();

  // Update time displays
  const timeElements = document.querySelectorAll(
    "#currentTime, #dashboardTime"
  );
  timeElements.forEach((element) => {
    if (element) {
      element.textContent = now.toLocaleTimeString();
    }
  });

  // Update last sync time
  const lastSyncElement = document.getElementById("lastSync");
  if (lastSyncElement && loginTime) {
    const timeDiff = Math.floor((now - loginTime) / 60000);
    lastSyncElement.textContent = `${timeDiff} min ago`;
  }

  // Update login time in security events
  const lastLoginElement = document.getElementById("lastLoginTime");
  if (lastLoginElement && loginTime) {
    const timeDiff = Math.floor((now - loginTime) / 60000);
    lastLoginElement.textContent = `${timeDiff} minutes ago`;
  }

  // Simulate live working hours
  const liveTimeElements = document.querySelectorAll(".live-time");
  liveTimeElements.forEach((element) => {
    if (
      element.textContent.includes("h") &&
      element.textContent !== element.dataset.originalText
    ) {
      // This would contain logic to update working hours in real-time
    }
  });
}

// Enhanced RFID Scanning Simulation
function simulateRFIDScan() {
  const verificationPanel = document.getElementById("verificationPanel");
  const scanBtn = document.getElementById("scanRFID");

  verificationPanel.classList.add("scanning-active");
  verificationPanel.innerHTML = `
        <div class="text-center">
            <div class="loading-spinner" style="margin: 0 auto 1rem; width: 3rem; height: 3rem; border: 3px solid var(--info); border-top: 3px solid transparent;"></div>
            <h4>Scanning RFID Card...</h4>
            <p class="card-description">Please hold card near scanner</p>
        </div>
    `;

  scanBtn.disabled = true;
  scanBtn.textContent = "Scanning...";

  setTimeout(() => {
    // Simulate successful scan
    verificationPanel.classList.remove("scanning-active");
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
    scanBtn.innerHTML =
      '<span class="icon">📱</span><span class="ml-2">Scan RFID</span>';

    showNotification("RFID scan successful - Welcome Christel!", "success");

    // Add to recent scans
    const recentScans = document.getElementById("recentScans");
    if (recentScans) {
      const newScan = document.createElement("div");
      newScan.innerHTML = `
                <div class="flex items-center justify-between" style="margin-bottom: 0.5rem;">
                    <span style="font-size: 0.875rem;">Christel - RC001</span>
                    <span class="badge badge-success">✓</span>
                </div>
            `;
      recentScans.querySelector(".space-y-2").prepend(newScan);
    }

    setTimeout(() => {
      verificationPanel.innerHTML = `
                <div class="text-center">
                    <div class="icon icon-xl mb-4">📱</div>
                    <h4>Ready for Verification</h4>
                    <p class="card-description">Tap RFID card or click Scan RFID to begin</p>
                </div>
            `;
    }, 3000);
  }, 2000);
}

// Enhanced Timesheet Functions
function approveTimesheet(button) {
  const row = button.closest("tr");
  const statusBadge = row.querySelector(".timesheet-status");
  const actionCell = row.querySelector("td:last-child");

  statusBadge.className = "badge badge-success";
  statusBadge.textContent = "Approved";

  actionCell.innerHTML = `
        <button class="btn btn-ghost" onclick="showNotification('Timesheet details view', 'info')">
            <span class="icon">👁️</span> View
        </button>
        <button class="btn btn-ghost" onclick="showNotification('Export timesheet', 'info')">
            <span class="icon">📄</span> Export
        </button>
    `;

  showNotification("Timesheet approved successfully", "success");
}

function approveAllTimesheets() {
  const pendingButtons = document.querySelectorAll(".approve-btn");
  let count = 0;

  pendingButtons.forEach((button) => {
    setTimeout(() => {
      approveTimesheet(button);
      count++;
    }, count * 300);
  });

  setTimeout(() => {
    showNotification(
      `All ${count} timesheets approved successfully`,
      "success"
    );
  }, count * 300 + 500);
}

// Enhanced Payroll Processing
function processPayroll() {
  showNotification("Processing payroll for research team...", "info");

  setTimeout(() => {
    showNotification(
      "Payroll processed successfully! ₱278,064 net distributed.",
      "success"
    );
  }, 2000);
}

// Enhanced Report Generation
function generateReport(type) {
  const reportNames = {
    payroll: "Payroll Analysis Report",
    attendance: "Attendance Patterns Report",
    compliance: "Philippine Labor Law Compliance Report",
  };

  showNotification(`Generating ${reportNames[type]}...`, "info");

  setTimeout(() => {
    showNotification(`${reportNames[type]} generated successfully!`, "success");
  }, 1500);
}

// Enhanced Notification System
function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.textContent = message;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.transform = "translateX(100%)";
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

// Enhanced Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // Login functionality
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      // In a real Django app, this form would submit to the server.
      // The JS login() function is a placeholder for the prototype.
      // For a real form submission, you would remove the e.preventDefault()
      // and the call to login(), and let the form submit normally.
      login();
    });
  }

  if (logoutBtn) {
    logoutBtn.addEventListener("click", logout);
  }

  // Password toggle
  if (togglePassword) {
    togglePassword.addEventListener("click", function () {
      const type =
        passwordField.getAttribute("type") === "password" ? "text" : "password";
      passwordField.setAttribute("type", type);
      eyeIcon.textContent = type === "password" ? "👁️" : "🙈";
    });
  }

  // Tab navigation
  document.querySelectorAll(".nav-item").forEach((item) => {
    item.addEventListener("click", function () {
      const tabName = this.getAttribute("data-tab");
      switchTab(tabName);
    });
  });

  // Subtab navigation
  document.querySelectorAll("[data-subtab]").forEach((trigger) => {
    trigger.addEventListener("click", function () {
      const subtabName = this.getAttribute("data-subtab");
      switchSubtab(subtabName);
    });
  });

  // RFID Scan button
  const scanBtn = document.getElementById("scanRFID");
  if (scanBtn) {
    scanBtn.addEventListener("click", simulateRFIDScan);
  }

  // Initialize progress bars animation
  setTimeout(function () {
    document.querySelectorAll(".progress-fill").forEach((fill) => {
      const width = fill.style.width;
      fill.style.width = "0%";
      setTimeout(() => {
        fill.style.width = width;
      }, 500);
    });
  }, 1000);

  // Keyboard shortcuts
  document.addEventListener("keydown", function (e) {
    if (e.altKey) {
      switch (e.key) {
        case "1":
          switchTab("dashboard");
          break;
        case "2":
          switchTab("employees");
          break;
        case "3":
          switchTab("attendance");
          break;
        case "4":
          switchTab("payroll");
          break;
        case "5":
          switchTab("reports");
          break;
        case "6":
          switchTab("research");
          break;
      }
    }
  });
});

// Enhanced Utility Functions
function formatCurrency(amount) {
  return new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
}

function formatCurrencyCompact(amount) {
  if (amount >= 1000000) {
    return "₱" + (amount / 1000000).toFixed(1) + "M";
  } else if (amount >= 1000) {
    return "₱" + (amount / 1000).toFixed(0) + "K";
  }
  return "₱" + amount.toLocaleString();
}
function clockIn(empId) {
  fetch("attendance_mark.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      employee_id: empId,
      action: "time_in",
      date: "<?= $today ?>",
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("timein-" + empId).textContent = data.time_in;
        document.getElementById("status-" + empId).textContent = "Present";
        loadLiveAttendance();
      } else alert(data.message);
    });
}

function clockOut(empId) {
  fetch("attendance_mark.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      employee_id: empId,
      action: "time_out",
      date: "<?= $today ?>",
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success)
        document.getElementById("timeout-" + empId).textContent = data.time_out;
      else alert(data.message);
      loadLiveAttendance();
    });
}

function submitManual() {
  const employeeId = document.getElementById("employee_id").value;
  if (!employeeId) return alert("Enter Employee ID");
  fetch("record_attendance.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `employee_id=${employeeId}`,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("manualResult").innerText = `${
          data.employee_name
        } Time ${data.type.toUpperCase()} recorded`;
        loadLiveAttendance();
      } else alert(data.message);
    });
}

function verifyFace(empId) {
  // Show camera container
  const container = document.getElementById("cameraContainer");
  container.style.display = "block";
  const video = document.getElementById("liveCamera");
  navigator.mediaDevices.getUserMedia({ video: true }).then((stream) => {
    video.srcObject = stream;
    video.play();
  });
  // Countdown + capture + send to record_attendance_face.php can be integrated here
}

// System initialization
console.log("🏗️ MEPFS PayrollPro HTML System Loaded");
console.log("📊 Research System: Richwell Colleges Capstone Project");
console.log(
  "👥 Research Team: Christel Arpon (Lead), Janico Castillo, Angelica Villarisco, Lealene Fajardo"
);
console.log(
  "🎯 Objectives: RFID + Biometric Integration, Fraud Prevention, Philippine Labor Law Compliance"
);
console.log(
  "⚡ Features: Employee Management, Attendance Tracking, Payroll Processing, Research Compliance"
);

// Performance monitoring
const perfData = performance.now();
console.log(`⚡ System loaded in ${perfData.toFixed(2)}ms`);
