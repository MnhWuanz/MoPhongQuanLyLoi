// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ========== MODAL FUNCTIONS ==========
function openModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Sinh Viên Mới';
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('studentModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('studentModal').style.display = 'none';
}

function openEditModal(id) {
    fetch(`/students/${id}`)
        .then(response => response.json())
        .then(result => {
            if (!result.success) {
                throw new Error(result.message || 'Không thể tải dữ liệu');
            }
            
            const student = result.student;
            
            document.getElementById('modalTitle').textContent = 'Sửa Thông Tin Sinh Viên';
            document.getElementById('studentId').value = student.id;
            document.getElementById('student_code').value = student.student_code || '';
            document.getElementById('full_name').value = student.full_name || '';
            document.getElementById('email').value = student.email || '';
            document.getElementById('dob').value = student.dob || '';
            document.getElementById('class_name').value = student.class_name || '';
            document.getElementById('score1').value = student.score1 !== null ? student.score1 : 0;
            document.getElementById('score2').value = student.score2 !== null ? student.score2 : 0;
            document.getElementById('score3').value = student.score3 !== null ? student.score3 : 0;
            
            document.getElementById('studentModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error loading student:', error);
            alert('Lỗi khi tải dữ liệu sinh viên: ' + error.message);
        });
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
    const modal = document.getElementById('studentModal');
    if (event.target === modal) {
        closeModal();
    }
}

// ========== SEARCH FUNCTION ==========
function searchStudents() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('studentTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
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
    
    fetch(`/students/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            window.location.reload();
        } else {
            alert('Lỗi: ' + result.message);
        }
    })
    .catch(error => {
        alert('Lỗi: ' + error.message);
    });
}

// ========== FORM SUBMIT ==========
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const studentId = document.getElementById('studentId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Đang lưu...';
            
            // Chuyển FormData thành JSON
            const data = {};
            formData.forEach((value, key) => {
                if (key !== '_token' && key !== '_method') {
                    data[key] = value;
                }
            });
            
            // Xác định URL và method
            const url = studentId ? `/students/${studentId}` : '/students';
            const method = studentId ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    closeModal();
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (result.message || 'Có lỗi xảy ra'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                alert('Lỗi: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
});
