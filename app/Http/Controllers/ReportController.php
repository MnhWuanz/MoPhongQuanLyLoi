<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $totalStudents = Student::count();
        $averageGPA = Student::avg('gpa');
        
        // Phân loại học lực
        $gradeDistribution = [
            'Xuất sắc' => Student::where('gpa', '>=', 3.6)->count(),
            'Giỏi' => Student::whereBetween('gpa', [3.0, 3.59])->count(),
            'Khá' => Student::whereBetween('gpa', [2.5, 2.99])->count(),
            'Trung bình' => Student::whereBetween('gpa', [2.0, 2.49])->count(),
            'Yếu' => Student::where('gpa', '<', 2.0)->count(),
        ];
        
        // Top 10 sinh viên xuất sắc
        $topStudents = Student::orderBy('gpa', 'desc')
            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();
        
        // Phân tích xu hướng điểm theo từng kỳ
        $scoreTrends = Student::select(
            DB::raw('AVG(score1) as avg_score1'),
            DB::raw('AVG(score2) as avg_score2'),
            DB::raw('AVG(score3) as avg_score3')
        )->first();
        
        // Phân tích theo lớp
        $classSummary = Student::select('class_name')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('AVG(gpa) as avg_gpa')
            ->selectRaw('AVG(score) as avg_score')
            ->groupBy('class_name')
            ->orderBy('avg_gpa', 'desc')
            ->get();
        
        // Sinh viên cần cải thiện (GPA < 2.5)
        $needImprovement = Student::where('gpa', '<', 2.5)
            ->orderBy('gpa', 'asc')
            ->get();
        
        // Phân tích xu hướng cải thiện (so sánh điểm giữa các kỳ)
        $improvementTrends = Student::select('id', 'student_code', 'full_name', 'score1', 'score2', 'score3', 'gpa')
            ->get()
            ->map(function ($student) {
                $trend = 'stable';
                if ($student->score3 > $student->score2 && $student->score2 > $student->score1) {
                    $trend = 'improving';
                } elseif ($student->score3 < $student->score2 && $student->score2 < $student->score1) {
                    $trend = 'declining';
                }
                
                $student->trend = $trend;
                $student->improvement = $student->score3 - $student->score1;
                return $student;
            });
        
        $improving = $improvementTrends->where('trend', 'improving')->sortByDesc('improvement');
        $declining = $improvementTrends->where('trend', 'declining')->sortBy('improvement');
        
        return view('reports.index', compact(
            'totalStudents',
            'averageGPA',
            'gradeDistribution',
            'topStudents',
            'scoreTrends',
            'classSummary',
            'needImprovement',
            'improving',
            'declining'
        ));
    }
    
    public function exportPDF()
    {
        // Lấy tất cả dữ liệu giống như index()
        $totalStudents = Student::count();
        $averageGPA = Student::avg('gpa');
        
        $gradeDistribution = [
            'Xuất sắc' => Student::where('gpa', '>=', 3.6)->count(),
            'Giỏi' => Student::whereBetween('gpa', [3.0, 3.59])->count(),
            'Khá' => Student::whereBetween('gpa', [2.5, 2.99])->count(),
            'Trung bình' => Student::whereBetween('gpa', [2.0, 2.49])->count(),
            'Yếu' => Student::where('gpa', '<', 2.0)->count(),
        ];
        
        $topStudents = Student::orderBy('gpa', 'desc')
            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();
        
        $scoreTrends = Student::select(
            DB::raw('AVG(score1) as avg_score1'),
            DB::raw('AVG(score2) as avg_score2'),
            DB::raw('AVG(score3) as avg_score3')
        )->first();
        
        $classSummary = Student::select('class_name')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('AVG(gpa) as avg_gpa')
            ->selectRaw('AVG(score) as avg_score')
            ->groupBy('class_name')
            ->orderBy('avg_gpa', 'desc')
            ->get();
        
        $needImprovement = Student::where('gpa', '<', 2.5)
            ->orderBy('gpa', 'asc')
            ->get();
        
        $improvementTrends = Student::select('id', 'student_code', 'full_name', 'score1', 'score2', 'score3', 'gpa')
            ->get()
            ->map(function ($student) {
                $trend = 'stable';
                if ($student->score3 > $student->score2 && $student->score2 > $student->score1) {
                    $trend = 'improving';
                } elseif ($student->score3 < $student->score2 && $student->score2 < $student->score1) {
                    $trend = 'declining';
                }
                
                $student->trend = $trend;
                $student->improvement = $student->score3 - $student->score1;
                return $student;
            });
        
        $improving = $improvementTrends->where('trend', 'improving')->sortByDesc('improvement');
        $declining = $improvementTrends->where('trend', 'declining')->sortBy('improvement');
        
        // Tạo PDF
        $pdf = PDF::loadView('reports.pdf', compact(
            'totalStudents',
            'averageGPA',
            'gradeDistribution',
            'topStudents',
            'scoreTrends',
            'classSummary',
            'needImprovement',
            'improving',
            'declining'
        ));
        
        $pdf->setPaper('a4', 'portrait');
        
        // Tải xuống
        return $pdf->download('bao-cao-hoc-tap-' . date('Y-m-d') . '.pdf');
    }
}
