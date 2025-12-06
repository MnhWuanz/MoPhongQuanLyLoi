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
    document.getElementById('gpa').value = student.gpa || 0;
    
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
