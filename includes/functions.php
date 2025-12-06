<?php
require_once __DIR__ . '/../config/database.php';

// ========== LECTURER FUNCTIONS ==========

// Tạo tài khoản giảng viên
function createLecturer($email, $password, $full_name) {
    try {
        $pdo = getConnection();
        
        // Kiểm tra email tồn tại
        $stmt = $pdo->prepare("SELECT id FROM lecturers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email đã tồn tại!'];
        }
        
        // Hash password để bảo mật
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert
        $stmt = $pdo->prepare("
            INSERT INTO lecturers (email, password, full_name) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $hashedPassword, $full_name]);
        
        return [
            'success' => true, 
            'message' => 'Đăng ký thành công!',
            'id' => $pdo->lastInsertId()
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Đăng nhập
function loginLecturer($email, $password) {
    try {
        $pdo = getConnection();
        
        $query = "SELECT * FROM lecturers WHERE email = '$email' AND password = '$password'";
        $stmt = $pdo->query($query);
        $user = $stmt->fetch();
        
        // Sử dụng password_verify() để kiểm tra mật khẩu đã hash
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = 'lecturer';
            
            return ['success' => true, 'message' => 'Đăng nhập thành công!'];
        } else {
            return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng!'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Đổi mật khẩu
function changePassword($userId, $oldPassword, $newPassword) {
    try {
        $pdo = getConnection();
        
        // Lấy mật khẩu hiện tại
        $stmt = $pdo->prepare("SELECT password FROM lecturers WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng!'];
        }
        
        // Kiểm tra mật khẩu cũ
        if (!password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Mật khẩu cũ không đúng!'];
        }
        
        // Hash mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Cập nhật
        $stmt = $pdo->prepare("UPDATE lecturers SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
        
        return ['success' => true, 'message' => 'Đổi mật khẩu thành công!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// ========== STUDENT FUNCTIONS ==========

// Hàm tính điểm trung bình từ 3 kỳ
function calculateAverageScore($score1, $score2, $score3) {
    return round(($score1 + $score2 + $score3) / 3, 2);
}

// Hàm chuyển đổi điểm sang GPA thang 4
function convertScoreToGPA($score) {
    if ($score >= 9.0) return 4.0;
    if ($score >= 8.5) return 3.7;
    if ($score >= 8.0) return 3.5;
    if ($score >= 7.5) return 3.3;
    if ($score >= 7.0) return 3.0;
    if ($score >= 6.5) return 2.7;
    if ($score >= 6.0) return 2.5;
    if ($score >= 5.5) return 2.3;
    if ($score >= 5.0) return 2.0;
    if ($score >= 4.0) return 1.5;
    return 0.0;
}

// Hàm lấy xếp loại theo GPA
function getGradeClassification($gpa) {
    if ($gpa >= 3.6) return ['text' => 'Xuất sắc', 'class' => 'excellent'];
    if ($gpa >= 3.0) return ['text' => 'Giỏi', 'class' => 'good'];
    if ($gpa >= 2.5) return ['text' => 'Khá', 'class' => 'fair'];
    if ($gpa >= 2.0) return ['text' => 'Trung bình', 'class' => 'average'];
    if ($gpa >= 1.0) return ['text' => 'Yếu', 'class' => 'weak'];
    return ['text' => 'Kém', 'class' => 'poor'];
}

// Lấy tất cả sinh viên
function getAllStudents() {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT * FROM students ORDER BY id ASC");
        $students = $stmt->fetchAll();
        
        return ['success' => true, 'students' => $students];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage(), 'students' => []];
    }
}

// Lấy sinh viên theo ID
function getStudentById($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        
        if ($student) {
            return ['success' => true, 'student' => $student];
        }
        return ['success' => false, 'message' => 'Không tìm thấy sinh viên!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Thêm sinh viên
function createStudent($student_code, $full_name, $email, $dob = null, $class_name = null, $score1 = 0, $score2 = 0, $score3 = 0) {
    try {
        $pdo = getConnection();
        
        // Kiểm tra mã SV tồn tại
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_code = ?");
        $stmt->execute([$student_code]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Mã sinh viên đã tồn tại!'];
        }
        
        // Validate ngày sinh không được lớn hơn ngày hiện tại
        if ($dob && strtotime($dob) > time()) {
            return ['success' => false, 'message' => 'Ngày sinh không hợp lệ! Không được lớn hơn ngày hiện tại.'];
        }
        
        // Tính điểm TB và GPA tự động
        $score = calculateAverageScore($score1, $score2, $score3);
        $gpa = convertScoreToGPA($score);
        
        // Insert
        $stmt = $pdo->prepare("
            INSERT INTO students (student_code, full_name, email, dob, class_name, score1, score2, score3, score, gpa) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$student_code, $full_name, $email, $dob, $class_name, $score1, $score2, $score3, $score, $gpa]);
        
        return [
            'success' => true, 
            'message' => 'Thêm sinh viên thành công!',
            'id' => $pdo->lastInsertId()
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Cập nhật sinh viên
function updateStudent($id, $student_code, $full_name, $email, $dob = null, $class_name = null, $score1 = 0, $score2 = 0, $score3 = 0) {
    try {
        $pdo = getConnection();
        
        // Kiểm tra mã SV trùng (trừ chính nó)
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_code = ? AND id != ?");
        $stmt->execute([$student_code, $id]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Mã sinh viên đã tồn tại!'];
        }
        
        // Validate ngày sinh không được lớn hơn ngày hiện tại
        if ($dob && strtotime($dob) > time()) {
            return ['success' => false, 'message' => 'Ngày sinh không hợp lệ! Không được lớn hơn ngày hiện tại.'];
        }
        
        // Tính điểm TB và GPA tự động
        $score = calculateAverageScore($score1, $score2, $score3);
        $gpa = convertScoreToGPA($score);
        
        // Update
        $stmt = $pdo->prepare("
            UPDATE students 
            SET student_code = ?, full_name = ?, email = ?, dob = ?, class_name = ?, score1 = ?, score2 = ?, score3 = ?, score = ?, gpa = ?
            WHERE id = ?
        ");
        $stmt->execute([$student_code, $full_name, $email, $dob, $class_name, $score1, $score2, $score3, $score, $gpa, $id]);
        
        return ['success' => true, 'message' => 'Cập nhật thành công!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Xóa sinh viên
function deleteStudent($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        
        return ['success' => true, 'message' => 'Xóa sinh viên thành công!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Tìm kiếm sinh viên
function searchStudents($keyword) {
    try {
        $pdo = getConnection();
        $keyword = "%$keyword%";
        
        $stmt = $pdo->prepare("
            SELECT * FROM students 
            WHERE student_code LIKE ? 
               OR full_name LIKE ? 
               OR email LIKE ? 
               OR class_name LIKE ?
            ORDER BY id ASC
        ");
        $stmt->execute([$keyword, $keyword, $keyword, $keyword]);
        $students = $stmt->fetchAll();
        
        return ['success' => true, 'students' => $students];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage(), 'students' => []];
    }
}
?>
