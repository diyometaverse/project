<?php
session_start();
include "db.php"; // ensure database connection is correct

// 🔔 Toast Function
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

// 🧾 Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        $_SESSION['toast_message'] = 'Please fill in both email and password.';
        $_SESSION['toast_type'] = 'error';
        header("Location: login.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, email, password, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin' || $user['role'] === 'hr') {
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['toast_message'] = 'Access restricted to admin and HR only.';
                $_SESSION['toast_type'] = 'error';
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['toast_message'] = 'Incorrect password.';
            $_SESSION['toast_type'] = 'error';
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['toast_message'] = 'No account found for this email.';
        $_SESSION['toast_type'] = 'error';
        header("Location: login.php");
        exit;
    }
}

?>

<!-- ✅ HTML Structure -->
<link rel="stylesheet" href="styles.css">
<style>
/* 🔔 Toast styles */
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
.custom-toast.show {
    opacity: 1;
    transform: translateY(0);
}
.custom-toast.error {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}
.custom-toast.success {
    background: linear-gradient(135deg, #16a34a, #15803d);
}
.custom-toast.warning {
    background: linear-gradient(135deg, #ca8a04, #a16207);
}
.custom-toast.info {
    background: linear-gradient(135deg, #2563eb, #1e40af);
}
</style>

<div id="loginScreen" class="login-container">
    <div class="space-y-6" style="width: 100%; max-width: 28rem;">
        <div class="login-header space-y-2">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="login-logo">
                    <span style="color: white; font-size: 2rem;">🏗️</span>
                </div>
                <div class="text-left">
                    <h1 class="gradient-text">MEPFS PayrollPro</h1>
                    <p style="font-size: 0.875rem; color: var(--muted-foreground);">Research System Portal</p>
                </div>
            </div>
            <div class="space-y-1">
                <h2 style="color: #1f2937;">Research System Access</h2>
                <p style="color: var(--muted-foreground);">Please enter your admin credentials to access the MEPFS research dashboard.</p>
            </div>
        </div>

        <!-- Login Form -->
        <div class="card login-card">
            <div class="card-header">
                <div class="card-title flex items-center gap-2">
                    <span class="icon" style="color: #2563eb;">🛡️</span>
                    Research Admin Login
                </div>
                <p class="card-description">Secure access to MEPFS payroll research system</p>
            </div>
            <div class="card-content">
                <form id="loginForm" class="space-y-4" method="POST" action="login.php">
                    <div class="space-y-2">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="input" placeholder="admin@mepfs.com" required>
                    </div>

                    <div class="space-y-2">
                        <label for="password">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="input pr-10" placeholder="Enter your password" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <span id="eyeIcon">👁️</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <input id="remember" type="checkbox" style="width: 1rem; height: 1rem;">
                            <label for="remember" style="font-size: 0.875rem; color: var(--muted-foreground);">Remember me</label>
                        </div>
                        <button type="button" style="font-size: 0.875rem; color: var(--muted-foreground); background: none; border: none; cursor: pointer;">
                            Forgot Password?
                        </button>
                    </div>

                    <button type="submit" id="loginBtn" class="btn btn-gradient w-full">
                        <span id="loginText">Sign In to Dashboard</span>
                        <div id="loginSpinner" class="loading-spinner hidden"></div>
                    </button>
                </form>
            </div>
        </div>

        <div class="card security-notice">
            <div class="card-content" style="padding-top: 1rem;">
                <div class="flex items-start gap-3">
                    <span class="icon" style="color: #2563eb; margin-top: 0.125rem;">🛡️</span>
                    <div class="space-y-1">
                        <p style="font-size: 0.875rem; font-weight: var(--font-weight-medium); color: #1e3a8a;">Secure Access</p>
                        <p style="font-size: 0.75rem; color: #1e40af;">
                            This system uses RFID + Biometric verification for enhanced security. 
                            All sessions are encrypted and monitored.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center space-y-2">
            <div class="footer-icons">
                <span>🔧 Mechanical</span>
                <span>⚡ Electrical</span>
                <span>🚿 Plumbing</span>
                <span>🔥 Fire Protection</span>
                <span>🚰 Sanitary</span>
            </div>
            <p style="font-size: 0.75rem; color: var(--muted-foreground);">
                MEPFS PayrollPro © 2025 | Authorized Personnel Only
            </p>
            <p style="font-size: 0.75rem; color: var(--muted-foreground); opacity: 0.8;">
                Capstone Research Project - Richwell Colleges, Incorporated
            </p>
            <p style="font-size: 0.75rem; color: var(--muted-foreground); opacity: 0.8;">
                Bachelor of Science in Information Systems
            </p>
        </div>
    </div>
</div>

<script>
// 👁️ Password Toggle
document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.textContent = "🙈";
    } else {
        passwordInput.type = "password";
        eyeIcon.textContent = "👁️";
    }
});
</script>
<?php if (isset($_SESSION['toast_message'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const toast = document.createElement("div");
    toast.className = "custom-toast <?php echo $_SESSION['toast_type']; ?>";
    toast.innerHTML = "<span><?php echo $_SESSION['toast_message']; ?></span>";
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add("show"), 100);
    setTimeout(() => toast.classList.remove("show"), 4000);
    setTimeout(() => toast.remove(), 4500);
});
</script>
<?php
unset($_SESSION['toast_message']);
unset($_SESSION['toast_type']);
endif;
?>
