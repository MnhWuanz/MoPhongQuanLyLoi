// Hàm hiển thị thông báo lỗi
function showError(message) {
    const errorDiv = document.getElementById('error-message') || createMessageDiv('error-message', '#ff6b6b');
    errorDiv.textContent = '❌ ' + message;
    errorDiv.style.display = 'block';
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 4000);
}

// Hàm hiển thị thông báo thành công
function showSuccess(message) {
    const successDiv = document.getElementById('success-message') || createMessageDiv('success-message', '#51cf66');
    successDiv.textContent = '✅ ' + message;
    successDiv.style.display = 'block';
    setTimeout(() => {
        successDiv.style.display = 'none';
    }, 3000);
}

// Tạo div thông báo
function createMessageDiv(id, bgColor) {
    const div = document.createElement('div');
    div.id = id;
    div.style.cssText = `
        display: none;
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: ${bgColor};
        color: white;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        animation: slideDown 0.3s ease;
    `;
    document.querySelector('.login-box form').prepend(div);
    return div;
}

document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const fullname = document.getElementById('fullname').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const agreeTerms = document.getElementById('agree_terms').checked;
    
    // Validate
    if (!fullname || fullname.length < 3) {
        showError('Vui lòng nhập họ và tên đầy đủ (ít nhất 3 ký tự)!');
        document.getElementById('fullname').focus();
        return;
    }
    
    if (!agreeTerms) {
        showError('Vui lòng đồng ý với điều khoản sử dụng!');
        return;
    }
    
    if (password !== confirmPassword) {
        showError('Mật khẩu xác nhận không khớp!');
        document.getElementById('confirm_password').focus();
        return;
    }
    
    if (password.length < 6) {
        showError('Mật khẩu phải có ít nhất 6 ký tự!');
        document.getElementById('password').focus();
        return;
    }
    
    // Disable button
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang xử lý...';
    
    try {
        // Gọi API đăng ký
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                full_name: fullname,
                email: email,
                phone_number: phone,
                password: password
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Đăng ký thành công! Đang chuyển đến trang đăng nhập...');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 1500);
        } else {
            showError(result.message || 'Đăng ký thất bại!');
        }
    } catch (error) {
        // Demo mode - lưu vào localStorage
        console.warn('Backend chưa sẵn sàng, dùng chế độ demo');
        
        // Kiểm tra email đã tồn tại
        const existingUsers = JSON.parse(localStorage.getItem('registeredUsers') || '[]');
        const emailExists = existingUsers.some(user => user.email === email);
        
        if (emailExists) {
            showError('Email đã được đăng ký!');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Đăng ký';
            return;
        }
        
        // Lưu user mới
        const newUser = {
            id: Date.now(),
            full_name: fullname,
            email: email,
            phone_number: phone,
            password: password, // Trong thực tế sẽ mã hóa
            created_at: new Date().toISOString()
        };
        
        existingUsers.push(newUser);
        localStorage.setItem('registeredUsers', JSON.stringify(existingUsers));
        
        showSuccess('Đăng ký thành công! Đang chuyển đến trang đăng nhập...');
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 1500);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Đăng ký';
    }
});

// Validate email format
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailPattern.test(email)) {
        this.style.borderColor = '#ff6b6b';
        showError('Email không đúng định dạng!');
    } else if (email) {
        this.style.borderColor = '#51cf66';
    }
});

// Validate phone format
document.getElementById('phone').addEventListener('blur', function() {
    const phone = this.value.trim();
    const phonePattern = /^[0-9]{10,11}$/;
    
    if (phone && !phonePattern.test(phone)) {
        this.style.borderColor = '#ff6b6b';
        showError('Số điện thoại phải có 10-11 chữ số!');
    } else if (phone) {
        this.style.borderColor = '#51cf66';
    }
});

// Check password strength
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = document.getElementById('password-strength');
    
    if (!strength) {
        const strengthDiv = document.createElement('small');
        strengthDiv.id = 'password-strength';
        strengthDiv.style.display = 'block';
        strengthDiv.style.marginTop = '5px';
        this.parentElement.appendChild(strengthDiv);
    }
    
    const strengthIndicator = document.getElementById('password-strength');
    
    if (password.length === 0) {
        strengthIndicator.textContent = '';
        strengthIndicator.style.background = 'transparent';
    } else if (password.length < 6) {
        strengthIndicator.textContent = '⚠️ Quá yếu - Cần ít nhất 6 ký tự';
        strengthIndicator.style.color = '#fff';
        strengthIndicator.style.background = '#ff6b6b';
    } else if (password.length < 8) {
        strengthIndicator.textContent = '✓ Trung bình - Nên dùng 8+ ký tự';
        strengthIndicator.style.color = '#fff';
        strengthIndicator.style.background = '#ffd43b';
    } else {
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*]/.test(password);
        if (hasNumber && hasSpecial) {
            strengthIndicator.textContent = '✓ Rất mạnh - Bảo mật tốt!';
            strengthIndicator.style.color = '#fff';
            strengthIndicator.style.background = '#51cf66';
        } else {
            strengthIndicator.textContent = '✓ Mạnh - Có thể thêm ký tự đặc biệt';
            strengthIndicator.style.color = '#fff';
            strengthIndicator.style.background = '#94d82d';
        }
    }
});
