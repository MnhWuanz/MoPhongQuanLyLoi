@extends('layouts.app')

@section('title', 'Dashboard - Quản Lý Sinh Viên')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <div class="header-left">
            <h1>🎓 Quản Lý Sinh Viên</h1>
            <span class="hide-mobile">Xin chào, <strong>{{ Auth::user()->full_name }}</strong></span>
        </div>
        <div class="header-right">
            <span class="show-mobile" style="width: 100%; text-align: center; margin-bottom: 10px;">
                Xin chào, <strong>{{ Auth::user()->full_name }}</strong>
            </span>
            <a href="{{ route('reports.index') }}" class="btn btn-success">📊 Báo Cáo</a>
            <a href="{{ route('change-password') }}" class="btn btn-secondary">🔐 Đổi MK</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">🚪 Thoát</button>
            </form>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="controls">
            <button class="btn btn-primary" onclick="openModal()">
                ➕ Thêm Sinh Viên
            </button>
            <div class="search-box">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="🔍 Tìm kiếm sinh viên..." 
                    onkeyup="searchStudents()"
                >
            </div>
        </div>

        <div class="table-container">
            <div class="table-wrapper">
                <table id="studentTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Ngày Sinh</th>
                            <th>Lớp</th>
                            <th>Email</th>
                            <th>Kỳ 1</th>
                            <th>Kỳ 2</th>
                            <th>Kỳ 3</th>
                            <th>ĐTB</th>
                            <th>GPA</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                            @php
                                $grade = (object) $student->getGradeClassification();
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="no-wrap">{{ $student->student_code }}</td>
                                <td class="no-wrap">{{ $student->full_name }}</td>
                                <td class="no-wrap">{{ $student->dob ? $student->dob->format('d/m/Y') : '' }}</td>
                                <td>{{ $student->class_name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ number_format($student->score1, 2) }}</td>
                                <td>{{ number_format($student->score2, 2) }}</td>
                                <td>{{ number_format($student->score3, 2) }}</td>
                                <td>{{ number_format($student->score, 2) }}</td>
                                <td>
                                    <span class="grade-badge {{ $grade->class }}">
                                        {{ number_format($student->gpa, 2) }} - {{ $grade->text }}
                                    </span>
                                </td>
                                <td class="no-wrap">
                                    <button class="btn-edit" onclick="openEditModal({{ $student->id }})">✏️</button>
                                <button class="btn-delete" onclick="deleteStudent({{ $student->id }})">🗑️</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="text-align:center;padding:40px;color:#999;">
                                Chưa có sinh viên. Hãy thêm sinh viên mới!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Sinh Viên -->
<div id="studentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Thêm Sinh Viên</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form id="studentForm">
            @csrf
            <div class="modal-body">
                <input type="hidden" id="studentId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_code">Mã sinh viên <span class="required">*</span></label>
                        <input type="text" id="student_code" name="student_code" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Họ và tên <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">Ngày sinh</label>
                        <input type="date" id="dob" name="dob">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="class_name">Lớp</label>
                    <input type="text" id="class_name" name="class_name">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="score1">Điểm Kỳ 1</label>
                        <input type="number" id="score1" name="score1" min="0" max="10" step="0.01" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="score2">Điểm Kỳ 2</label>
                        <input type="number" id="score2" name="score2" min="0" max="10" step="0.01" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="score3">Điểm Kỳ 3</label>
                        <input type="number" id="score3" name="score3" min="0" max="10" step="0.01" value="0">
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-success">Lưu</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Set CSRF token for AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});

const API_BASE = '{{ url("/students") }}';
</script>
@endpush
@endsection
