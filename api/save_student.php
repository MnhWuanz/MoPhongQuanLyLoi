<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Yêu cầu đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $student_code = trim($_POST['student_code'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $class_name = trim($_POST['class_name'] ?? '');
    $gpa = floatval($_POST['gpa'] ?? 0);
    
    // Validate
    if (empty($student_code) || empty($full_name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
        exit;
    }
    
    if ($id) {
        // Cập nhật
        $result = updateStudent($id, $student_code, $full_name, $email, $dob, $class_name, $gpa);
    } else {
        // Thêm mới
        $result = createStudent($student_code, $full_name, $email, $dob, $class_name, $gpa);
    }
    
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
