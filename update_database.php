<?php
// Script tự động cập nhật database - Thêm cột score1, score2, score3
require_once __DIR__ . '/config/database.php';

echo "🔧 Đang cập nhật cấu trúc database...\n\n";

try {
    $pdo = getConnection();
    
    // Kiểm tra và thêm cột score1
    echo "1. Kiểm tra cột score1... ";
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN score1 DECIMAL(4,2) DEFAULT 0 AFTER email");
        echo "✅ Đã thêm cột score1\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️ Cột score1 đã tồn tại\n";
        } else {
            throw $e;
        }
    }
    
    // Kiểm tra và thêm cột score2
    echo "2. Kiểm tra cột score2... ";
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN score2 DECIMAL(4,2) DEFAULT 0 AFTER score1");
        echo "✅ Đã thêm cột score2\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️ Cột score2 đã tồn tại\n";
        } else {
            throw $e;
        }
    }
    
    // Kiểm tra và thêm cột score3
    echo "3. Kiểm tra cột score3... ";
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN score3 DECIMAL(4,2) DEFAULT 0 AFTER score2");
        echo "✅ Đã thêm cột score3\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️ Cột score3 đã tồn tại\n";
        } else {
            throw $e;
        }
    }
    
    // Kiểm tra và thêm cột score nếu chưa có
    echo "\n4. Kiểm tra cột score... ";
    try {
        $pdo->exec("ALTER TABLE students ADD COLUMN score DECIMAL(4,2) DEFAULT 0 AFTER score3");
        echo "✅ Đã thêm cột score\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️ Cột score đã tồn tại\n";
        } else {
            throw $e;
        }
    }
    
    // Cập nhật dữ liệu cũ
    echo "5. Cập nhật dữ liệu cũ (nếu có)... ";
    try {
        $stmt = $pdo->exec("
            UPDATE students 
            SET score1 = COALESCE(score, 0), 
                score2 = COALESCE(score, 0), 
                score3 = COALESCE(score, 0)
            WHERE (score1 = 0 OR score1 IS NULL) 
              AND (score2 = 0 OR score2 IS NULL) 
              AND (score3 = 0 OR score3 IS NULL)
              AND score > 0
        ");
        echo "✅ Đã cập nhật $stmt dòng\n";
    } catch (Exception $e) {
        echo "⚠️ Bỏ qua (không có dữ liệu cũ)\n";
    }
    
    // Hiển thị cấu trúc bảng
    echo "\n📊 Cấu trúc bảng students:\n";
    echo str_repeat("-", 80) . "\n";
    $stmt = $pdo->query("DESCRIBE students");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo sprintf("%-20s %-20s %s\n", $col['Field'], $col['Type'], $col['Null']);
    }
    
    // Hiển thị dữ liệu mẫu
    echo "\n📝 Dữ liệu mẫu (5 sinh viên đầu tiên):\n";
    echo str_repeat("-", 80) . "\n";
    $stmt = $pdo->query("SELECT id, student_code, full_name, score1, score2, score3, score, gpa FROM students LIMIT 5");
    $students = $stmt->fetchAll();
    
    if (empty($students)) {
        echo "Chưa có dữ liệu sinh viên.\n";
    } else {
        foreach ($students as $student) {
            echo sprintf(
                "ID: %d | Mã: %s | Tên: %s\n  K1: %.2f | K2: %.2f | K3: %.2f | TB: %.2f | GPA: %.2f\n",
                $student['id'],
                $student['student_code'],
                $student['full_name'],
                $student['score1'],
                $student['score2'],
                $student['score3'],
                $student['score'],
                $student['gpa']
            );
        }
    }
    
    echo "\n✅ Cập nhật database hoàn tất!\n";
    echo "🎉 Bây giờ bạn có thể sử dụng hệ thống bình thường.\n";
    
} catch (Exception $e) {
    echo "\n❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
?>
