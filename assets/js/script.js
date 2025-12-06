// ========== GPA CALCULATION ==========
function calculateGPA() {
    const score1 = parseFloat(document.getElementById('score1').value) || 0;
    const score2 = parseFloat(document.getElementById('score2').value) || 0;
    const score3 = parseFloat(document.getElementById('score3').value) || 0;
    
    // Tính điểm trung bình 3 kỳ
    const avgScore = (score1 + score2 + score3) / 3;
    const scoreInput = document.getElementById('score');
    const gpaInput = document.getElementById('gpa');
    const ratingInput = document.getElementById('rating');
    
    scoreInput.value = avgScore.toFixed(2);
    
    let gpa = 0;
    let rating = '';
    
    // Công thức chuyển đổi điểm trung bình sang GPA thang 4
    if (avgScore >= 9.0) {
        gpa = 4.0;
        rating = 'Xuất sắc';
    } else if (avgScore >= 8.5) {
        gpa = 3.7;
        rating = 'Xuất sắc';
    } else if (avgScore >= 8.0) {
        gpa = 3.5;
        rating = 'Giỏi';
    } else if (avgScore >= 7.5) {
        gpa = 3.3;
        rating = 'Giỏi';
    } else if (avgScore >= 7.0) {
        gpa = 3.0;
        rating = 'Khá';
    } else if (avgScore >= 6.5) {
        gpa = 2.7;
        rating = 'Khá';
    } else if (avgScore >= 6.0) {
        gpa = 2.5;
        rating = 'Khá';
    } else if (avgScore >= 5.5) {
        gpa = 2.3;
        rating = 'Trung bình';
    } else if (avgScore >= 5.0) {
        gpa = 2.0;
        rating = 'Trung bình';
    } else if (avgScore >= 4.0) {
        gpa = 1.5;
        rating = 'Yếu';
    } else {
        gpa = 0.0;
        rating = 'Kém';
    }
    
    gpaInput.value = gpa.toFixed(2);
    ratingInput.value = rating;
    
    // Màu sắc cho xếp loại
    if (gpa >= 3.5) {
        ratingInput.style.color = '#27ae60';
    } else if (gpa >= 3.0) {
        ratingInput.style.color = '#3498db';
    } else if (gpa >= 2.5) {
        ratingInput.style.color = '#f39c12';
    } else if (gpa >= 2.0) {
        ratingInput.style.color = '#e67e22';
    } else {
        ratingInput.style.color = '#e74c3c';
    }
}

// ========== MODAL FUNCTIONS ==========
function openModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Sinh Viên Mới';
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('modal').classList.add('show');
}

function closeModal() {
    document.getElementById('modal').classList.remove('show');
}

function editStudent(student) {
    document.getElementById('modalTitle').textContent = 'Sửa Thông Tin Sinh Viên';
    document.getElementById('studentId').value = student.id;
    document.getElementById('student_code').value = student.student_code;
    document.getElementById('full_name').value = student.full_name;
    document.getElementById('dob').value = student.dob || '';
    document.getElementById('class_name').value = student.class_name || '';
    document.getElementById('email').value = student.email;
    document.getElementById('score1').value = student.score1 || 0;
    document.getElementById('score2').value = student.score2 || 0;
    document.getElementById('score3').value = student.score3 || 0;
    document.getElementById('score').value = student.score || 0;
    document.getElementById('gpa').value = student.gpa || 0;
    
    // Tính lại GPA và rating
    calculateGPA();
    
    document.getElementById('modal').classList.add('show');
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        closeModal();
    }
}

// ========== NOTIFICATION ==========
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// ========== SEARCH FUNCTION ==========
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('studentTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent || row.innerText;
        
        if (text.toLowerCase().indexOf(filter) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// ========== DELETE FUNCTION ==========
function deleteStudent(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa sinh viên này?')) {
        return;
    }
    
    fetch('api/delete_student.php?id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification(result.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(result.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Lỗi: ' + error.message, 'error');
    });
}

// ========== FORM SUBMIT ==========
const form = document.getElementById('studentForm');
if (form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ Đang lưu...';
        
        fetch('api/save_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification(result.message, 'success');
                closeModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(result.message, 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        })
        .catch(error => {
            showNotification('Lỗi: ' + error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
}
