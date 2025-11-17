<?php
include "db.php";
session_start();

function showToast($message, $type = 'error') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.createElement('div');
            toast.className = 'custom-toast ' + '$type';
            toast.innerHTML = '<span>$message</span>';
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => toast.classList.remove('show'), 4000);
            setTimeout(() => toast.remove(), 4500);
        });
    </script>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $role = trim($_POST["role"] ?? 'staff');

    if (empty($email) || empty($password)) {
        showToast('Please fill in all fields.');
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            showToast('Email already exists.');
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed, $role);

            if ($stmt->execute()) {
                showToast('Account created successfully!', 'success');
            } else {
                showToast('Error creating account.');
            }
        }
    }
}
?>

<link rel="stylesheet" href="styles.css">
<style>
.custom-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(31, 41, 55, 0.9);
    color: #fff;
    padding: 12px 18px;
    border-radius: 8px;
    font-size: 0.9rem;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.4s ease;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.custom-toast.show { opacity: 1; transform: translateY(0); }
.custom-toast.error { background: linear-gradient(135deg, #dc2626, #b91c1c); }
.custom-toast.success { background: linear-gradient(135deg, #16a34a, #15803d); }
</style>

<div class="login-container" style="max-width: 28rem; margin: 4rem auto;">
    <div class="card">
        <div class="card-header">
            <h2>Create Account (Testing Only)</h2>
            <p class="card-description">For testing the login system.</p>
        </div>
        <div class="card-content">
            <form method="POST" class="space-y-4">
                <div>
                    <label>Email Address</label>
                    <input type="email" name="email" class="input" placeholder="admin@mepfs.com" required>
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="password" class="input" placeholder="Enter password" required>
                </div>
                <div>
                    <label>Role</label>
                    <select name="role" class="input" required>
                        <option value="admin">Admin</option>
                        <option value="hr">HR</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-gradient w-full">Create Account</button>
            </form>
        </div>
    </div>
</div>
