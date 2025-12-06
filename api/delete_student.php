<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Yêu cầu đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit;
}

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID!']);
    exit;
}

$result = deleteStudent($id);
echo json_encode($result);
?>
