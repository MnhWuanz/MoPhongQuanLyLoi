-- Thêm các cột điểm kỳ vào bảng students
USE simple_student_management;

-- Thêm cột score1, score2, score3 nếu chưa tồn tại
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS score1 DECIMAL(4,2) DEFAULT 0 AFTER email;

ALTER TABLE students 
ADD COLUMN IF NOT EXISTS score2 DECIMAL(4,2) DEFAULT 0 AFTER score1;

ALTER TABLE students 
ADD COLUMN IF NOT EXISTS score3 DECIMAL(4,2) DEFAULT 0 AFTER score2;

-- Cập nhật dữ liệu cũ (nếu có) - chia đều điểm cũ cho 3 kỳ
UPDATE students 
SET score1 = score, 
    score2 = score, 
    score3 = score 
WHERE (score1 IS NULL OR score1 = 0) 
  AND (score2 IS NULL OR score2 = 0) 
  AND (score3 IS NULL OR score3 = 0)
  AND score > 0;

-- Kiểm tra cấu trúc bảng
DESCRIBE students;

-- Xem dữ liệu mẫu
SELECT id, student_code, full_name, score1, score2, score3, score, gpa FROM students LIMIT 5;
