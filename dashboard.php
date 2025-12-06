<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

// Yêu cầu đăng nhập
requireLogin();
$user = getCurrentUser();

// Lấy danh sách sinh viên
$result = getAllStudents();
$students = $result['students'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản Lý Sinh Viên</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <header class="header">
            <h1>🎓 Quản Lý Sinh Viên</h1>
            <div class="user-info">
                <span>Xin chào, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></span>
                <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
            </div>
        </header>
        
        <!-- Toolbar -->
        <div class="toolbar">
            <div class="search-box">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="🔍 Tìm kiếm theo mã SV, tên, email, lớp..."
                    onkeyup="searchTable()"
                >
            </div>
            <button class="btn btn-success" onclick="openModal()">➕ Thêm Sinh Viên</button>
        </div>
        
        <!-- Notification -->
        <div id="notification" class="notification" style="display:none;"></div>
        
        <!-- Table -->
        <div class="table-wrapper">
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã SV</th>
                        <th>Họ và Tên</th>
                        <th>Ngày Sinh</th>
                        <th>Lớp</th>
                        <th>Email</th>
                        <th>GPA</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:40px;color:#999;">
                                Chưa có sinh viên. Hãy thêm sinh viên mới!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($student['student_code']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo $student['dob'] ? date('d/m/Y', strtotime($student['dob'])) : ''; ?></td>
                                <td><?php echo htmlspecialchars($student['class_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo number_format($student['gpa'], 2); ?></td>
                                <td class="actions">
                                    <button class="btn btn-edit" onclick='editStudent(<?php echo json_encode($student); ?>)'>✏️</button>
                                    <button class="btn btn-delete" onclick="deleteStudent(<?php echo $student['id']; ?>)">🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Thêm Sinh Viên Mới</h2>
            
            <form id="studentForm" method="POST" action="api/save_student.php">
                <input type="hidden" id="studentId" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã SV <span class="required">*</span></label>
                        <input type="text" name="student_code" id="student_code" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="full_name" id="full_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="dob" id="dob">
                    </div>
                    
                    <div class="form-group">
                        <label>Lớp</label>
                        <input type="text" name="class_name" id="class_name">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>GPA (0-4)</label>
                        <input type="number" name="gpa" id="gpa" min="0" max="4" step="0.01" value="0">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">💾 Lưu</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
