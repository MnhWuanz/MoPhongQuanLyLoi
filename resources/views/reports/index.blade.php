@extends('layouts.app')

@section('title', 'Báo Cáo Học Tập')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <div class="header-left">
            <h1>📊 Báo Cáo Học Tập & Phân Tích</h1>
        </div>
        <div class="header-right">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Quay lại Dashboard</a>
            <a href="{{ route('reports.export') }}" class="btn btn-primary">📥 Xuất PDF</a>
        </div>
    </header>

    <div class="dashboard-content">
        <!-- Thống kê tổng quan -->
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3>{{ $totalStudents }}</h3>
                    <p>Tổng sinh viên</p>
                </div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <h3>{{ number_format($averageGPA, 2) }}</h3>
                    <p>GPA trung bình</p>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <h3>{{ $gradeDistribution['Xuất sắc'] }}</h3>
                    <p>Sinh viên xuất sắc</p>
                </div>
            </div>
            <div class="stat-card stat-danger">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3>{{ $needImprovement->count() }}</h3>
                    <p>Cần cải thiện</p>
                </div>
            </div>
        </div>

        <!-- Phân loại học lực -->
        <div class="report-section">
            <h2>📊 Phân Loại Học Lực</h2>
            <div class="chart-container">
                <div class="grade-chart">
                    @foreach($gradeDistribution as $grade => $count)
                        @php
                            $percentage = $totalStudents > 0 ? ($count / $totalStudents) * 100 : 0;
                            $colorClass = match($grade) {
                                'Xuất sắc' => 'grade-xuat-sac',
                                'Giỏi' => 'grade-gioi',
                                'Khá' => 'grade-kha',
                                'Trung bình' => 'grade-trung-binh',
                                'Yếu' => 'grade-yeu',
                                default => ''
                            };
                        @endphp
                        <div class="grade-bar">
                            <div class="grade-label">
                                <span class="grade-name">{{ $grade }}</span>
                                <span class="grade-count">{{ $count }} SV ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Xu hướng điểm theo kỳ -->
        <div class="report-section">
            <h2>📈 Xu Hướng Điểm Trung Bình Theo Kỳ</h2>
            <div class="trend-chart">
                <div class="trend-item">
                    <div class="trend-label">Kỳ 1</div>
                    <div class="trend-score">{{ number_format($scoreTrends->avg_score1, 2) }}</div>
                </div>
                <div class="trend-arrow">→</div>
                <div class="trend-item">
                    <div class="trend-label">Kỳ 2</div>
                    <div class="trend-score">{{ number_format($scoreTrends->avg_score2, 2) }}</div>
                </div>
                <div class="trend-arrow">→</div>
                <div class="trend-item">
                    <div class="trend-label">Kỳ 3</div>
                    <div class="trend-score">{{ number_format($scoreTrends->avg_score3, 2) }}</div>
                </div>
            </div>
            <div class="trend-analysis">
                @php
                    $trend1to2 = $scoreTrends->avg_score2 - $scoreTrends->avg_score1;
                    $trend2to3 = $scoreTrends->avg_score3 - $scoreTrends->avg_score2;
                @endphp
                <p>
                    <strong>Phân tích:</strong>
                    @if($trend1to2 > 0 && $trend2to3 > 0)
                        <span class="text-success">✅ Xu hướng cải thiện liên tục qua các kỳ</span>
                    @elseif($trend1to2 < 0 && $trend2to3 < 0)
                        <span class="text-danger">⚠️ Xu hướng giảm, cần có biện pháp can thiệp</span>
                    @else
                        <span class="text-warning">📊 Xu hướng không ổn định</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Top sinh viên xuất sắc -->
        <div class="report-section">
            <h2>🏆 Top 10 Sinh Viên Xuất Sắc</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Hạng</th>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Lớp</th>
                            <th>GPA</th>
                            <th>ĐTB</th>
                            <th>Xếp Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topStudents as $index => $student)
                            @php
                                $grade = (object) $student->getGradeClassification();
                            @endphp
                            <tr>
                                <td>
                                    @if($index == 0)
                                        🥇
                                    @elseif($index == 1)
                                        🥈
                                    @elseif($index == 2)
                                        🥉
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>{{ $student->student_code }}</td>
                                <td><strong>{{ $student->full_name }}</strong></td>
                                <td>{{ $student->class_name }}</td>
                                <td><strong>{{ number_format($student->gpa, 2) }}</strong></td>
                                <td>{{ number_format($student->score, 2) }}</td>
                                <td><span class="grade-badge {{ $grade->class }}">{{ $grade->text }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân tích theo lớp -->
        <div class="report-section">
            <h2>🎓 Phân Tích Theo Lớp</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Lớp</th>
                            <th>Số SV</th>
                            <th>GPA TB</th>
                            <th>Điểm TB</th>
                            <th>Đánh Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classSummary as $class)
                            <tr>
                                <td><strong>{{ $class->class_name ?: 'Chưa phân lớp' }}</strong></td>
                                <td>{{ $class->total }}</td>
                                <td><strong>{{ number_format($class->avg_gpa, 2) }}</strong></td>
                                <td>{{ number_format($class->avg_score, 2) }}</td>
                                <td>
                                    @if($class->avg_gpa >= 3.5)
                                        <span class="badge-success">Xuất sắc</span>
                                    @elseif($class->avg_gpa >= 3.0)
                                        <span class="badge-info">Giỏi</span>
                                    @elseif($class->avg_gpa >= 2.5)
                                        <span class="badge-warning">Khá</span>
                                    @else
                                        <span class="badge-secondary">Trung bình</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sinh viên có xu hướng cải thiện -->
        <div class="report-section">
            <h2>📈 Sinh Viên Có Xu Hướng Cải Thiện</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Kỳ 1</th>
                            <th>Kỳ 2</th>
                            <th>Kỳ 3</th>
                            <th>Mức Cải Thiện</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($improving->take(10) as $student)
                            <tr>
                                <td>{{ $student->student_code }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ number_format($student->score1, 2) }}</td>
                                <td>{{ number_format($student->score2, 2) }}</td>
                                <td>{{ number_format($student->score3, 2) }}</td>
                                <td>
                                    <span class="badge-success">
                                        +{{ number_format($student->improvement, 2) }} điểm
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <p>Không có sinh viên nào có xu hướng cải thiện liên tục</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sinh viên có xu hướng giảm -->
        <div class="report-section">
            <h2>📉 Sinh Viên Cần Can Thiệp (Xu Hướng Giảm)</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Kỳ 1</th>
                            <th>Kỳ 2</th>
                            <th>Kỳ 3</th>
                            <th>Mức Giảm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($declining->take(10) as $student)
                            <tr>
                                <td>{{ $student->student_code }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ number_format($student->score1, 2) }}</td>
                                <td>{{ number_format($student->score2, 2) }}</td>
                                <td>{{ number_format($student->score3, 2) }}</td>
                                <td>
                                    <span class="badge-danger">
                                        {{ number_format($student->improvement, 2) }} điểm
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <p>✅ Không có sinh viên nào có xu hướng giảm điểm</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sinh viên cần cải thiện -->
        @if($needImprovement->count() > 0)
        <div class="report-section">
            <h2>⚠️ Danh Sách Sinh Viên Cần Hỗ Trợ (GPA < 2.5)</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Lớp</th>
                            <th>GPA</th>
                            <th>ĐTB</th>
                            <th>Xếp Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($needImprovement as $student)
                            @php
                                $grade = (object) $student->getGradeClassification();
                            @endphp
                            <tr>
                                <td>{{ $student->student_code }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->class_name }}</td>
                                <td><strong>{{ number_format($student->gpa, 2) }}</strong></td>
                                <td>{{ number_format($student->score, 2) }}</td>
                                <td><span class="grade-badge {{ $grade->class }}">{{ $grade->text }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    font-size: 48px;
    opacity: 0.8;
}

.stat-content h3 {
    font-size: 36px;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #7f8c8d;
    font-size: 14px;
}

.report-section {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.report-section h2 {
    color: #2c3e50;
    font-size: 24px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
}

.grade-chart {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grade-bar {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.grade-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    font-size: 14px;
}

.grade-name {
    color: #2c3e50;
}

.grade-count {
    color: #7f8c8d;
    font-size: 13px;
}

.progress-bar {
    height: 30px;
    background: #ecf0f1;
    border-radius: 15px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    transition: width 1s ease-out;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    color: white;
    font-weight: 600;
    font-size: 13px;
}

.trend-chart {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    padding: 40px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 16px;
}

.trend-item {
    text-align: center;
    background: white;
    padding: 30px 40px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    min-width: 150px;
}

.trend-label {
    font-size: 14px;
    color: #7f8c8d;
    margin-bottom: 10px;
    font-weight: 600;
}

.trend-score {
    font-size: 36px;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.trend-arrow {
    font-size: 36px;
    color: #667eea;
    font-weight: 700;
}

.trend-analysis {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #667eea;
}

.trend-analysis p {
    margin: 0;
    font-size: 15px;
    line-height: 1.6;
}

.text-success {
    color: #27ae60;
    font-weight: 600;
}

.text-danger {
    color: #e74c3c;
    font-weight: 600;
}

.text-warning {
    color: #f39c12;
    font-weight: 600;
}

.badge-success, .badge-info, .badge-warning, .badge-secondary, .badge-danger {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}

.badge-success {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
}

.badge-info {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.badge-secondary {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    color: white;
}

.badge-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .report-section {
        padding: 20px;
    }
    
    .report-section h2 {
        font-size: 20px;
    }
    
    .trend-chart {
        flex-direction: column;
        padding: 30px 15px;
        gap: 20px;
    }
    
    .trend-item {
        padding: 20px 30px;
        min-width: 120px;
    }
    
    .trend-arrow {
        transform: rotate(90deg);
    }
    
    .grade-chart {
        gap: 15px;
    }
    
    .progress-bar {
        height: 25px;
    }
    
    .progress-fill {
        font-size: 12px;
        padding-right: 8px;
    }
}

@media (max-width: 600px) {
    .stats-grid {
        gap: 15px;
    }
    
    .stat-card {
        padding: 18px;
        gap: 15px;
    }
    
    .stat-icon {
        font-size: 32px;
    }
    
    .stat-content h3 {
        font-size: 24px;
    }
    
    .stat-content p {
        font-size: 13px;
    }
    
    .report-section {
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .report-section h2 {
        font-size: 18px;
        margin-bottom: 20px;
    }
    
    .trend-item {
        padding: 15px 20px;
    }
    
    .trend-score {
        font-size: 28px;
    }
    
    .trend-arrow {
        font-size: 28px;
    }
    
    .grade-label {
        font-size: 13px;
    }
    
    .progress-bar {
        height: 22px;
    }
    
    .progress-fill {
        font-size: 11px;
    }
    
    .badge-success, .badge-info, .badge-warning, .badge-secondary, .badge-danger {
        padding: 5px 10px;
        font-size: 12px;
    }
    
    table {
        font-size: 12px;
    }
    
    table th,
    table td {
        padding: 8px 6px;
    }
}

@media (max-width: 480px) {
    .report-section h2 {
        font-size: 16px;
        padding-bottom: 12px;
    }
    
    .trend-chart {
        padding: 20px 10px;
    }
    
    .trend-item {
        padding: 12px 15px;
        min-width: 100px;
    }
    
    .trend-label {
        font-size: 12px;
    }
    
    .trend-score {
        font-size: 24px;
    }
    
    .trend-arrow {
        font-size: 24px;
    }
    
    .trend-analysis {
        padding: 15px;
        font-size: 14px;
    }
}

/* Landscape orientation */
@media (max-height: 500px) and (orientation: landscape) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .report-section {
        margin-bottom: 15px;
    }
}

/* Print styles for reports */
@media print {
    .dashboard-header,
    .btn {
        display: none !important;
    }
    
    .report-section {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .trend-chart {
        background: white;
        border: 1px solid #ddd;
    }
}
</style>
@endpush
@endsection
