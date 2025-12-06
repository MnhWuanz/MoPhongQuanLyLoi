# 🎓 Hệ Thống Quản Lý Sinh Viên - PHP

## 📋 Giới thiệu

Hệ thống quản lý sinh viên hoàn chỉnh được xây dựng bằng **PHP thuần** với MySQL database. Hệ thống cung cấp đầy đủ tính năng CRUD (Create, Read, Update, Delete) cho quản lý thông tin sinh viên.

## ✨ Tính năng

### 🔐 Authentication
- ✅ Đăng ký tài khoản giảng viên
- ✅ Đăng nhập với email và mật khẩu
- ✅ Session management
- ✅ Bảo mật với password hashing (bcrypt)
- ✅ Đăng xuất

### 👨‍🎓 Quản lý sinh viên
- ✅ **Thêm** sinh viên mới
- ✅ **Sửa** thông tin sinh viên
- ✅ **Xóa** sinh viên (có xác nhận)
- ✅ **Tìm kiếm** theo mã SV, tên, email, lớp
- ✅ Hiển thị danh sách sinh viên dạng bảng
- ✅ Validation đầy đủ

### 🎨 Giao diện
- ✅ Responsive design (mobile-friendly)
- ✅ Gradient backgrounds đẹp mắt
- ✅ Modal popup cho thêm/sửa
- ✅ Thông báo realtime
- ✅ Smooth animations

## 📁 Cấu trúc dự án

```
MoPhongQuanLyLoi/
├── config/
│   ├── database.php        # Cấu hình kết nối MySQL
│   └── session.php         # Quản lý session
├── includes/
│   └── functions.php       # Các hàm xử lý logic
├── api/
│   ├── save_student.php    # API thêm/sửa sinh viên
│   └── delete_student.php  # API xóa sinh viên
├── assets/
│   ├── css/
│   │   └── style.css       # Stylesheet chính
│   └── js/
│       └── script.js       # JavaScript chính
├── index.php               # Trang chủ (redirect)
├── login.php               # Trang đăng nhập
├── register.php            # Trang đăng ký
├── dashboard.php           # Trang quản lý sinh viên
├── logout.php              # Xử lý đăng xuất
└── README.md               # Tài liệu này
```

## 🔧 Cài đặt

### 1️⃣ Yêu cầu hệ thống
- **PHP** >= 7.4
- **MySQL** >= 5.7 hoặc MariaDB
- **Apache/Nginx** web server
- **XAMPP/WAMP/MAMP** (khuyến nghị)

### 2️⃣ Cấu hình Database

**Bước 1:** Mở file `config/database.php` và cập nhật thông tin:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'simple_student_management');
```

**Bước 2:** Tạo database và bảng:

```sql
CREATE DATABASE simple_student_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simple_student_management;

-- Bảng giảng viên
CREATE TABLE lecturers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng sinh viên
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_code VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    dob DATE,
    class_name VARCHAR(50),
    email VARCHAR(100) NOT NULL,
    gpa DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dữ liệu mẫu sinh viên
INSERT INTO students (student_code, full_name, dob, class_name, email, gpa) VALUES
('SV001', 'Nguyễn Văn An', '2002-03-15', 'CNTT01', 'an.nv@student.edu.vn', 3.45),
('SV002', 'Trần Thị Bình', '2002-07-22', 'CNTT01', 'binh.tt@student.edu.vn', 3.78),
('SV003', 'Lê Văn Cường', '2003-01-10', 'CNTT02', 'cuong.lv@student.edu.vn', 3.22);
```

### 3️⃣ Chạy ứng dụng

#### 🔸 Cách 1: Sử dụng XAMPP/WAMP

1. Copy thư mục `MoPhongQuanLyLoi` vào:
   - XAMPP: `C:\xampp\htdocs\`
   - WAMP: `C:\wamp64\www\`

2. Start Apache và MySQL trong XAMPP/WAMP Control Panel

3. Truy cập: `http://localhost/MoPhongQuanLyLoi`

#### 🔸 Cách 2: PHP Built-in Server

```powershell
cd d:\MoPhongQuanLyLoi
php -S localhost:8000
```

Truy cập: `http://localhost:8000`

## 📖 Hướng dẫn sử dụng

### 1. Đăng ký tài khoản
1. Truy cập trang đăng ký: `register.php`
2. Nhập đầy đủ thông tin giảng viên
3. Click **"Đăng Ký"**
4. Hệ thống tự động đăng nhập và chuyển đến Dashboard

### 2. Đăng nhập
1. Truy cập: `login.php`
2. Nhập email và mật khẩu
3. Click **"Đăng Nhập"**

### 3. Quản lý sinh viên

#### ➕ Thêm sinh viên mới
1. Click nút **"➕ Thêm Sinh Viên"**
2. Điền thông tin vào form
3. Click **"💾 Lưu"**

#### ✏️ Sửa thông tin
1. Click nút **"✏️"** trên hàng sinh viên cần sửa
2. Cập nhật thông tin
3. Click **"💾 Lưu"**

#### 🗑️ Xóa sinh viên
1. Click nút **"🗑️"** trên hàng sinh viên cần xóa
2. Xác nhận xóa trong popup
3. Sinh viên sẽ bị xóa khỏi database

#### 🔍 Tìm kiếm
- Nhập từ khóa vào ô tìm kiếm
- Kết quả lọc realtime theo:
  - Mã sinh viên
  - Họ tên
  - Email
  - Lớp

## 🔒 Bảo mật

Hệ thống áp dụng các biện pháp bảo mật:

- ✅ **Password Hashing**: Sử dụng `password_hash()` với thuật toán bcrypt
- ✅ **SQL Injection Prevention**: PDO Prepared Statements
- ✅ **XSS Protection**: `htmlspecialchars()` cho mọi output
- ✅ **Session-based Authentication**: Bảo vệ các trang yêu cầu đăng nhập
- ✅ **CSRF Protection**: Session validation
- ✅ **Input Validation**: Cả client-side và server-side

## 🎯 API Endpoints

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| POST | `/api/save_student.php` | Thêm hoặc cập nhật sinh viên |
| POST | `/api/delete_student.php?id={id}` | Xóa sinh viên theo ID |

### Request Body (save_student.php)
```json
{
  "id": "1",              // Optional (có khi sửa, không có khi thêm)
  "student_code": "SV001",
  "full_name": "Nguyễn Văn A",
  "dob": "2002-01-15",
  "class_name": "CNTT01",
  "email": "a@student.edu.vn",
  "gpa": "3.5"
}
```

### Response
```json
{
  "success": true,
  "message": "Thêm sinh viên thành công!",
  "id": 1
}
```

## 🛠️ Công nghệ sử dụng

- **Backend**: PHP 7.4+ (thuần, không framework)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Security**: PDO, bcrypt, session management
- **Design**: Responsive, Mobile-first

## 🐛 Troubleshooting

### ❌ Lỗi kết nối database
```
Solution:
1. Kiểm tra MySQL đã chạy chưa
2. Verify thông tin trong config/database.php
3. Kiểm tra database đã tạo chưa
4. Test bằng: php -r "new PDO('mysql:host=localhost', 'root', '');"
```

### ❌ Session không hoạt động
```
Solution:
1. Kiểm tra session_start() được gọi
2. Xóa cookies và cache browser (Ctrl+Shift+Delete)
3. Kiểm tra quyền write folder session trong PHP
4. Verify session.save_path trong php.ini
```

### ❌ CSS/JS không load
```
Solution:
1. Kiểm tra đường dẫn tương đối trong HTML
2. Clear cache browser (Ctrl+F5)
3. Kiểm tra file tồn tại trong assets/
4. Xem Console (F12) để debug
```

### ❌ "Call to undefined function password_hash()"
```
Solution:
Cần PHP >= 5.5. Upgrade PHP version.
```

## 📊 Database Schema

### Table: `lecturers`
| Field | Type | Key | Description |
|-------|------|-----|-------------|
| id | INT | PK | ID tự tăng |
| email | VARCHAR(100) | UNIQUE | Email đăng nhập |
| password | VARCHAR(255) | | Password đã hash |
| full_name | VARCHAR(100) | | Họ tên |
| phone | VARCHAR(20) | | Số điện thoại |
| department | VARCHAR(100) | | Khoa |
| created_at | TIMESTAMP | | Thời gian tạo |

### Table: `students`
| Field | Type | Key | Description |
|-------|------|-----|-------------|
| id | INT | PK | ID tự tăng |
| student_code | VARCHAR(20) | UNIQUE | Mã sinh viên |
| full_name | VARCHAR(100) | | Họ tên |
| dob | DATE | | Ngày sinh |
| class_name | VARCHAR(50) | | Lớp |
| email | VARCHAR(100) | | Email |
| gpa | DECIMAL(3,2) | | Điểm GPA (0-4) |
| created_at | TIMESTAMP | | Thời gian tạo |

## 🚀 Tính năng sắp tới

- [ ] Upload ảnh đại diện sinh viên
- [ ] Export danh sách ra Excel/PDF
- [ ] Import sinh viên từ file CSV
- [ ] Thống kê, báo cáo
- [ ] Phân quyền (Admin/Lecturer/Student)
- [ ] Email notification
- [ ] Multi-language support
- [ ] API RESTful đầy đủ
- [ ] Pagination cho danh sách lớn

## 📞 Liên hệ & Hỗ trợ

- **GitHub**: [MnhWuanz/MoPhongQuanLyLoi](https://github.com/MnhWuanz/MoPhongQuanLyLoi)
- **Issues**: Tạo issue trên GitHub
- **Branch**: DEV

## 📄 License

MIT License - Free to use and modify

---

**Version:** 2.0.0 (PHP Pure)  
**Last Updated:** December 6, 2025  
**Author:** MnhWuanz  
**Language:** PHP (Pure), MySQL

## 🎉 Credits

Dự án được xây dựng hoàn toàn bằng PHP thuần, không sử dụng framework. Phù hợp cho:
- Học tập và nghiên cứu
- Dự án nhỏ và vừa
- Prototype nhanh
- Hiểu rõ cơ chế hoạt động

**Happy Coding! 💻✨**
#Nhom 10
Thực Thành Phân Tích Mã Nguồn Mở
