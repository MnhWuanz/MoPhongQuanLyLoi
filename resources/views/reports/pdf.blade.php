<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo Cáo Học Tập</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 11px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .stat-row {
            display: table-row;
        }
        
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        .stat-card h3 {
            font-size: 20px;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            font-size: 10px;
            color: #7f8c8d;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #667eea;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table th {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
        }
        
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .grade-badge {
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            display: inline-block;
        }
        
        .grade-xuat-sac {
            background: #2ecc71;
        }
        
        .grade-gioi {
            background: #3498db;
        }
        
        .grade-kha {
            background: #f39c12;
        }
        
        .grade-trung-binh {
            background: #95a5a6;
        }
        
        .grade-yeu {
            background: #e74c3c;
        }
        
        .trend-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .trend-item {
            display: inline-block;
            text-align: center;
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            margin-right: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .trend-label {
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .trend-score {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
        }
        
        .grade-bar {
            margin-bottom: 10px;
        }
        
        .grade-bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .grade-bar-progress {
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .grade-bar-fill {
            height: 100%;
            float: left;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-success {
            color: #27ae60;
            font-weight: bold;
        }
        
        .text-danger {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .text-warning {
            color: #f39c12;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📊 BÁO CÁO HỌC TẬP & PHÂN TÍCH XU HƯỚNG ĐIỂM</h1>
        <p>Ngày xuất: {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card">
                <h3>{{ $totalStudents }}</h3>
                <p>Tổng sinh viên</p>
            </div>
            <div class="stat-card">
                <h3>{{ number_format($averageGPA, 2) }}</h3>
                <p>GPA trung bình</p>
            </div>
            <div class="stat-card">
                <h3>{{ $gradeDistribution['Xuất sắc'] }}</h3>
                <p>Sinh viên xuất sắc</p>
            </div>
            <div class="stat-card">
                <h3>{{ $needImprovement->count() }}</h3>
                <p>Cần cải thiện</p>
            </div>
        </div>
    </div>

    <!-- Phân loại học lực -->
    <div class="section">
        <div class="section-title">PHÂN LOẠI HỌC LỰC</div>
        @foreach($gradeDistribution as $grade => $count)
            @php
                $percentage = $totalStudents > 0 ? ($count / $totalStudents) * 100 : 0;
            @endphp
            <div class="grade-bar">
                <div class="grade-bar-label">
                    <span><strong>{{ $grade }}</strong></span>
                    <span>{{ $count }} SV ({{ number_format($percentage, 1) }}%)</span>
                </div>
                <div class="grade-bar-progress">
                    <div class="grade-bar-fill" style="width: {{ $percentage }}%; background: 
                        @if($grade == 'Xuất sắc') #2ecc71
                        @elseif($grade == 'Giỏi') #3498db
                        @elseif($grade == 'Khá') #f39c12
                        @elseif($grade == 'Trung bình') #95a5a6
                        @else #e74c3c
                        @endif
                    "></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Xu hướng điểm -->
    <div class="section">
        <div class="section-title">XU HƯỚNG ĐIỂM TRUNG BÌNH THEO KỲ</div>
        <div class="trend-box">
            <div class="trend-item">
                <div class="trend-label">Kỳ 1</div>
                <div class="trend-score">{{ number_format($scoreTrends->avg_score1, 2) }}</div>
            </div>
            <div class="trend-item">
                <div class="trend-label">Kỳ 2</div>
                <div class="trend-score">{{ number_format($scoreTrends->avg_score2, 2) }}</div>
            </div>
            <div class="trend-item">
                <div class="trend-label">Kỳ 3</div>
                <div class="trend-score">{{ number_format($scoreTrends->avg_score3, 2) }}</div>
            </div>
        </div>
        @php
            $trend1to2 = $scoreTrends->avg_score2 - $scoreTrends->avg_score1;
            $trend2to3 = $scoreTrends->avg_score3 - $scoreTrends->avg_score2;
        @endphp
        <p style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <strong>Phân tích:</strong>
            @if($trend1to2 > 0 && $trend2to3 > 0)
                <span class="text-success">✓ Xu hướng cải thiện liên tục qua các kỳ</span>
            @elseif($trend1to2 < 0 && $trend2to3 < 0)
                <span class="text-danger">⚠ Xu hướng giảm, cần có biện pháp can thiệp</span>
            @else
                <span class="text-warning">≈ Xu hướng không ổn định</span>
            @endif
        </p>
    </div>

    <!-- Top sinh viên -->
    <div class="section page-break">
        <div class="section-title">TOP 10 SINH VIÊN XUẤT SẮC</div>
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
                        <td class="text-center">
                            @if($index == 0) 🥇
                            @elseif($index == 1) 🥈
                            @elseif($index == 2) 🥉
                            @else {{ $index + 1 }}
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

    <!-- Phân tích theo lớp -->
    <div class="section">
        <div class="section-title">PHÂN TÍCH THEO LỚP</div>
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
                                Xuất sắc
                            @elseif($class->avg_gpa >= 3.0)
                                Giỏi
                            @elseif($class->avg_gpa >= 2.5)
                                Khá
                            @else
                                Trung bình
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sinh viên cải thiện -->
    @if($improving->count() > 0)
    <div class="section page-break">
        <div class="section-title">SINH VIÊN CÓ XU HƯỚNG CẢI THIỆN (TOP 10)</div>
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
                @foreach($improving->take(10) as $student)
                    <tr>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ number_format($student->score1, 2) }}</td>
                        <td>{{ number_format($student->score2, 2) }}</td>
                        <td>{{ number_format($student->score3, 2) }}</td>
                        <td class="text-success">+{{ number_format($student->improvement, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Sinh viên giảm -->
    @if($declining->count() > 0)
    <div class="section">
        <div class="section-title">SINH VIÊN CẦN CAN THIỆP (XU HƯỚNG GIẢM)</div>
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
                @foreach($declining->take(10) as $student)
                    <tr>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ number_format($student->score1, 2) }}</td>
                        <td>{{ number_format($student->score2, 2) }}</td>
                        <td>{{ number_format($student->score3, 2) }}</td>
                        <td class="text-danger">{{ number_format($student->improvement, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Sinh viên cần hỗ trợ -->
    @if($needImprovement->count() > 0)
    <div class="section page-break">
        <div class="section-title">DANH SÁCH SINH VIÊN CẦN HỖ TRỢ (GPA < 2.5)</div>
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
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Hệ Thống Quản Lý Sinh Viên</strong></p>
        <p>Báo cáo được tạo tự động bởi hệ thống - {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
