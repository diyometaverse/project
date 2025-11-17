<?php
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch employees
$result = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

// header('Content-Type: application/json');
// echo json_encode($employees);

?>

<div id="tab-employees" class="tab-content">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1>Employee & User Management</h1>
                            <p class="card-description">Manage MEPFS construction project team members</p>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('addEmployeeModal')">
                            <span class="icon">➕</span>
                            <span class="ml-2">Add Employee</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 2fr 1fr;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Research Team Members</h3>
                            <p class="card-description">Capstone research participants from Richwell Colleges</p>
                        </div>
                        <div class="card-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Daily Rate</th>
                                        <th>Status</th>
                                        <th>RFID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTableBody">
                                    <tr><td colspan="6" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    // --- TEAM STATISTICS ---

                    $sql_total = "SELECT COUNT(*) as total FROM employees";
                    $total = $conn->query($sql_total)->fetch_assoc()['total'];

                    // Active members (status = active)
                    $sql_active = "SELECT COUNT(*) as active FROM employees WHERE status='active'";
                    $active = $conn->query($sql_active)->fetch_assoc()['active'];

                    // RFID assigned (where rfid_tag is not null)
                    $sql_rfid = "SELECT COUNT(*) as rfid FROM employees WHERE rfid_tag IS NOT NULL";
                    $rfid = $conn->query($sql_rfid)->fetch_assoc()['rfid'];

                    // Present today (from attendance table)
                    $today = date("Y-m-d");
                    $sql_present = "SELECT COUNT(DISTINCT employee_id) as present FROM attendance WHERE date='$today'";
                    $present = $conn->query($sql_present)->fetch_assoc()['present'];

                                        // --- DEPARTMENT DISTRIBUTION ---
                    $sql_dept = "SELECT department, COUNT(*) as count FROM employees GROUP BY department";
                    $result_dept = $conn->query($sql_dept);

                    $dept_data = [];
                    while ($row = $result_dept->fetch_assoc()) {
                        $dept_data[] = $row;
                    }
                    ?>
                    <div class="space-y-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Team Statistics</h3>
                            </div>
                            <div class="card-content space-y-4">
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>Active Members</span>
                                        <span><?= $active ?>/<?= $total ?></span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: <?= ($total>0? ($active/$total)*100 : 0) ?>%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>RFID Assigned</span>
                                        <span><?= $rfid ?>/<?= $total ?></span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: <?= ($total>0? ($rfid/$total)*100 : 0) ?>%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>Present Today</span>
                                        <span><?= $present ?>/<?= $total ?></span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: <?= ($total>0? ($present/$total)*100 : 0) ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $dept_icons = [
                            "Electrical" => "⚡",
                            "Fire Protection" => "🛡️",
                            "IT" => "🧑‍💻",
                            "Plumbing" => "💧",
                            // fallback if department not in list
                            "default" => "🏢"
                        ];
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Department Distribution</h3>
                            </div>
                            <div class="card-content space-y-2">
                                <?php foreach($dept_data as $d): 
                                    $percent = $total>0 ? round(($d['count']/$total)*100) : 0;
                                    $icon = $dept_icons[$d['department']] ?? $dept_icons["default"];
                                ?>
                                    <div class="flex justify-between">
                                        <span><?= $icon . " " . $d['department'] ?></span>
                                        <span><?= $percent ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>


                </div>
            </div>

            <div id="tab-employees" class="tab-content">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1>Employee & User Management</h1>
                            <p class="card-description">Manage MEPFS construction project team members</p>
                        </div>
                        <button class="btn btn-primary" onclick="openModal('addEmployeeModal')">
                            <span class="icon">➕</span>
                            <span class="ml-2">Add Employee</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 2fr 1fr;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Research Team Members</h3>
                            <p class="card-description">Capstone research participants from Richwell Colleges</p>
                        </div>
                        <div class="card-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Hourly Rate</th>
                                        <th>Status</th>
                                        <th>RFID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Team Statistics</h3>
                            </div>
                            <div class="card-content space-y-4">
                                <div>
                                    <div class="flex justify-between mb-2">
                                    <span>Active Members</span>
                                    <span></span>

                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: 100%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>RFID Assigned</span>
                                        <span>4/4</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: 100%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span>Present Today</span>
                                        <span>4/4</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-fill" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Department Distribution</h3>
                            </div>
                            <div class="card-content space-y-2">
                                <div class="flex justify-between">
                                    <span>⚡ Electrical</span>
                                    <span>25%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>🛡️ Fire Protection</span>
                                    <span>25%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>🔧 Mechanical</span>
                                    <span>25%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>💧 Plumbing</span>
                                    <span>25%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- Add Employee Modal -->
            <div id="addEmployeeModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="modal-content bg-white rounded-lg max-w-3xl p-6 relative shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Add New Employee</h2>
                    <form id="addEmployeeForm" method="POST" action="employee_add.php" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label>First Name</label>
                                <input type="text" name="first_name" class="input" required>
                            </div>
                            <div>
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="input" required>
                            </div>
                            <div>
                                <label>Email</label>
                                <input type="email" name="email" class="input">
                            </div>
                            <div>
                                <label>Phone</label>
                                <input type="text" name="phone" class="input">
                            </div>
                            <div>
                                <label>Department</label>
                                <input type="text" name="department" class="input" required>
                            </div>
                            <div>
                                <label>Position</label>
                                <input type="text" name="position" class="input" required>
                            </div>
                            <div>
                                <label>Daily Rate</label>
                                <input type="number" step="0.01" name="daily_rate" class="input">
                            </div>
                            <div>
                                <label>Date Hired</label>
                                <input type="date" name="date_hired" class="input" required>
                            </div>
                            <div>
                                <label>Status</label>
                                <select name="status" class="input">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label>RFID Tag</label>
                                <input type="text" name="rfid_tag" class="input">
                            </div>
                            <!-- <div>
                                <label>Face ID</label>
                                <input type="text" name="face_id" class="input">
                            </div> -->

                            <div class="mt-4">
                                <label class="font-bold">Face Registration</label>

                                <div class="mb-2">
                                    <select id="cameraSelector" class="border p-2 rounded w-full"></select>
                                </div>

                                <video id="addEmpCamera" autoplay playsinline style="width:100%; max-width:350px; border-radius:10px;"></video>

                                <button type="button" class="btn btn-primary mt-3" onclick="captureFaceImage()">Capture Face</button>

                                <div id="previewContainer" class="mt-3 hidden">
                                    <p class="font-bold">Captured Image:</p>
                                    <img id="capturedPreview" style="width:150px; border-radius:10px;">
                                </div>
                            </div>

                            <input type="hidden" id="captured_face_image" name="face_image">
                        </div>
                        <div class="flex justify-end mt-6 space-x-2">
                            <button type="button" class="btn btn-secondary" onclick="closeModals('addEmployeeModal')">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Employee</button>
                        </div>
                    </form>
            </div>
</div>
            <!-- View Employee Modal -->
<div id="viewEmployeeModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="modal-content bg-white rounded-lg max-w-3xl p-6 relative shadow-lg">
    <h2 class="text-2xl font-bold mb-4">View Employee</h2>
    <div id="viewEmployeeDetails" class="space-y-2 text-gray-700"></div>
    <div class="flex justify-end mt-6">
      <button class="btn btn-secondary" onclick="closeModals('viewEmployeeModal')">Close</button>
    </div>
  </div>
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="modal-content bg-white rounded-lg max-w-3xl p-6 relative shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Edit Employee</h2>
    <form id="editEmployeeForm" class="space-y-4">
      <input type="hidden" name="employee_id" id="edit_employee_id">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label>First Name</label>
          <input type="text" name="first_name" id="edit_first_name" class="input" required>
        </div>
        <div>
          <label>Last Name</label>
          <input type="text" name="last_name" id="edit_last_name" class="input" required>
        </div>
        <div>
          <label>Email</label>
          <input type="email" name="email" id="edit_email" class="input">
        </div>
        <div>
          <label>Phone</label>
          <input type="text" name="phone" id="edit_phone" class="input">
        </div>
        <div>
          <label>Department</label>
          <input type="text" name="department" id="edit_department" class="input">
        </div>
        <div>
          <label>Position</label>
          <input type="text" name="position" id="edit_position" class="input">
        </div>
        <div>
          <label>Daily Rate</label>
          <input type="number" step="0.01" name="daily_rate" id="edit_daily_rate" class="input">
        </div>
        <div>
          <label>Status</label>
          <select name="status" id="edit_status" class="input">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <label>RFID Tag</label>
          <input type="text" name="rfid_tag" id="edit_rfid_tag" class="input">
        </div>
        <div>
          <label>Face ID</label>
          <input type="text" name="face_id" id="edit_face_id" class="input">
        </div>
      </div>
      <div class="flex justify-end mt-6 space-x-2">
        <button type="button" class="btn btn-secondary" onclick="closeModals('editEmployeeModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<style>
        /* =============================
    MODAL BASE
    ============================= */
    .modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0,0,0,0.5);
        z-index: 50;
        transition: opacity 0.3s ease;
    }

    .modal.hidden {
        opacity: 0;
        pointer-events: none;
    }

    /* =============================
    MODAL CONTENT
    ============================= */
    .modal-content {
        max-height: 90vh;
        overflow-y: auto;
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        width: 100%;
        max-width: 720px;
        animation: slideUp 0.35s ease;
    }

    /* Modal animation */
    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* =============================
    FORM INPUTS
    ============================= */
    .input {
        display: block;
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.95rem;
        color: #374151;
        transition: all 0.2s ease;
    }

    .input:focus {
        outline: none;
        border-color: #7c3aed; /* purple */
        box-shadow: 0 0 0 2px rgba(124,58,237,0.3);
    }

    /* =============================
    BUTTONS
    ============================= */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 16px;
        transition: background 0.2s ease, transform 0.15s ease;
        cursor: pointer;
    }

    .btn-secondary {
        background-color: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background-color: #d1d5db;
    }

    /* =============================
    FORM LAYOUT
    ============================= */
    form label {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
        margin-bottom: 4px;
        display: inline-block;
    }

    form h2 {
        color: #1f2937;
    }

</style>
<script>
/* =============================
   CAMERA INITIALIZATION
============================= */

let liveStream = null;

// Load available cameras
async function loadCameraDevices() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(d => d.kind === "videoinput");

        const selector = document.getElementById("cameraSelector");
        selector.innerHTML = "";

        videoDevices.forEach((device, index) => {
            let opt = document.createElement("option");
            opt.value = device.deviceId;
            opt.textContent = device.label || `Camera ${index + 1}`;
            selector.appendChild(opt);
        });

    } catch (err) {
        console.error("Camera listing error:", err);
        showNotification("Unable to load cameras.", "error");
    }
}

// Start camera feed
async function startAddEmployeeCamera() {
    try {
        await loadCameraDevices();

        const selector = document.getElementById("cameraSelector");
        const deviceId = selector.value;

        // Stop previous stream safely
        if (liveStream) {
            liveStream.getTracks().forEach(t => t.stop());
        }

        liveStream = await navigator.mediaDevices.getUserMedia({
            video: { deviceId: deviceId ? { exact: deviceId } : undefined },
            audio: false
        });

        const video = document.getElementById("addEmpCamera");
        video.srcObject = liveStream;

    } catch (error) {
        console.error("Camera start error:", error);
        showNotification("Cannot access camera.", "error");
    }
}

// Change camera when user selects another device
document.getElementById("cameraSelector").addEventListener("change", startAddEmployeeCamera);


/* =============================
   IMAGE CAPTURE FOR DATABASE
============================= */

function captureFaceImage() {
    try {
        const video = document.getElementById("addEmpCamera");

        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert to base64 (safe for storing)
        const imageData = canvas.toDataURL("image/jpeg", 0.9);

        document.getElementById("captured_face_image").value = imageData;

        // Preview to user
        const preview = document.getElementById("capturedPreview");
        preview.src = imageData;

        document.getElementById("previewContainer").classList.remove("hidden");

        showNotification("Face image captured.", "success");

    } catch (err) {
        console.error("Capture error:", err);
        showNotification("Failed to capture image.", "error");
    }
}


/* =============================
   START CAMERA WHEN ADD EMPLOYEE MODAL OPENS
============================= */

function openAddEmployeeModal() {
    openModal('addEmployeeModal');
    startAddEmployeeCamera();
}
</script>
<script>
    // Open modal
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    // Close modal
    function closeModals(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function viewEmployee(id) {
    fetch(`employee_view.php?id=${id}`)
        .then(res => res.json())
        .then(emp => {
            if (emp.error) return alert(emp.error);

            const details = `
                <p><strong>Name:</strong> ${emp.first_name} ${emp.last_name}</p>
                <p><strong>Email:</strong> ${emp.email ?? '-'}</p>
                <p><strong>Phone:</strong> ${emp.phone ?? '-'}</p>
                <p><strong>Department:</strong> ${emp.department ?? '-'}</p>
                <p><strong>Position:</strong> ${emp.position ?? '-'}</p>
                <p><strong>Daily Rate:</strong> ₱${parseFloat(emp.daily_rate).toFixed(2)}</p>
                <p><strong>Status:</strong> ${emp.status}</p>
                <p><strong>RFID:</strong> ${emp.rfid_tag ?? '-'}</p>
                <p><strong>Face ID:</strong> ${emp.face_id ?? '-'}</p>
                <p><strong>Date Hired:</strong> ${emp.date_hired ?? '-'}</p>
            `;
            document.getElementById("viewEmployeeDetails").innerHTML = details;
            openModal('viewEmployeeModal');
        })
        .catch(err => console.error("Error:", err));
}

function editEmployee(id) {
    fetch(`employee_view.php?id=${id}`)
        .then(res => res.json())
        .then(emp => {
            if (emp.error) return alert(emp.error);

            document.getElementById("edit_employee_id").value = emp.employee_id;
            document.getElementById("edit_first_name").value = emp.first_name;
            document.getElementById("edit_last_name").value = emp.last_name;
            document.getElementById("edit_email").value = emp.email;
            document.getElementById("edit_phone").value = emp.phone;
            document.getElementById("edit_department").value = emp.department;
            document.getElementById("edit_position").value = emp.position;
            document.getElementById("edit_daily_rate").value = emp.daily_rate;
            document.getElementById("edit_status").value = emp.status;
            document.getElementById("edit_rfid_tag").value = emp.rfid_tag;
            document.getElementById("edit_face_id").value = emp.face_id;

            openModal('editEmployeeModal');
        })
        .catch(err => console.error("Error:", err));
}

// ✅ Handle edit form submission
document.getElementById("editEmployeeForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("employee_update.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeModals('editEmployeeModal');
            loadEmployees();
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(err => console.error("Error:", err));
});


</script>
<script>
    document.getElementById("addEmployeeForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("employee_add.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeModals('addEmployeeModal');
            loadEmployees(); // reload the employee table
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(err => console.error("Error:", err));
    });

    function loadEmployees() {
        fetch("employee_list.php") // this now returns PURE JSON
            .then(res => res.json())
            .then(data => {
                let tbody = document.getElementById("employeeTableBody");
                tbody.innerHTML = "";

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center">No employees found</td></tr>`;
                    return;
                }

                data.forEach(emp => {
                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <div>
                                    <strong>${emp.first_name} ${emp.last_name}</strong>
                                    <div class="card-description">EMP${emp.employee_id.toString().padStart(3,"0")} • ${emp.email ?? ''}</div>
                                    <div style="font-size: 0.75rem; color: var(--info);">${emp.position ?? ''}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-default">${emp.department ?? '-'}</span>
                            </td>
                            <td>₱${parseFloat(emp.daily_rate).toFixed(2)}/day</td>
                            <td>
                                <span class="badge ${emp.status === 'active' ? 'badge-success' : 'badge-destructive'}">${emp.status}</span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">${emp.rfid_tag ?? '-'}</span>
                            </td>
                            <td>
                                <button class="btn btn-ghost" onclick="editEmployee(${emp.employee_id})">✏️</button>
                                <button class="btn btn-ghost" onclick="viewEmployee(${emp.employee_id})">👁️</button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(err => {
                console.error("Error loading employees:", err);
                document.getElementById("employeeTableBody").innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error loading employees</td></tr>`;
            });
    }

    document.addEventListener("DOMContentLoaded", loadEmployees);
</script>

