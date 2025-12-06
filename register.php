<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect nếu đã đăng nhập
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Xử lý đăng ký
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    
    // Validate
    if (empty($email) || empty($password) || empty($fullName)) {
        $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } else {
        $result = createLecturer($email, $password, $fullName);
        if ($result['success']) {
            // Auto login
            loginLecturer($email, $password);
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Quản Lý Sinh Viên</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box register-box">
            <h1>🎓 Quản Lý Sinh Viên</h1>
            <h2>Đăng Ký Tài Khoản</h2>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">👤 Họ và tên <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        placeholder="Nguyễn Văn A"
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">📧 Email <span class="required">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="email@example.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">🔒 Mật khẩu <span class="required">*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ít nhất 6 ký tự"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">🔒 Xác nhận mật khẩu <span class="required">*</span></label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Nhập lại mật khẩu"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Đăng Ký</button>
            </form>
            
            <div class="auth-footer">
                Đã có tài khoản? <a href="login.php">Đăng nhập</a>
            </div>
        </div>
    </div>
</body>
</html>
