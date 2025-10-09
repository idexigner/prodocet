<?php

namespace App\Repositories;

use App\Interfaces\StudentInterface;
use App\Models\User;
use App\Models\StudentAcademicHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentRepository implements StudentInterface
{
    public function getAllStudents(Request $request)
    {
        $query = User::where('user_type', 'student')
            ->with(['roles', 'studentAcademicHours'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function getStudentById($id)
    {
        return User::where('user_type', 'student')
            ->with(['roles', 'studentAcademicHours', 'groupStudents.group'])
            ->findOrFail($id);
    }

    public function createStudent(array $data)
    {
        DB::beginTransaction();
        try {
            $data['user_type'] = 'student';
            $data['password'] = bcrypt($data['password'] ?? 'password123');
            
            $student = User::create($data);
            
            // Assign student role if not already assigned
            if (!$student->hasRole('student')) {
                $student->assignRole('student');
            }

            DB::commit();
            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStudent($id, array $data)
    {
        $student = User::where('user_type', 'student')->findOrFail($id);
        
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $student->update($data);
        return $student;
    }

    public function deleteStudent($id)
    {
        $student = User::where('user_type', 'student')->findOrFail($id);
        return $student->delete();
    }

    public function restoreStudent($id)
    {
        $student = User::where('user_type', 'student')->withTrashed()->findOrFail($id);
        return $student->restore();
    }

    public function getStudentsForDataTable(Request $request)
    {
        $query = User::where('user_type', 'student')
            ->with(['roles', 'studentAcademicHours']);

        return DataTables::of($query)
            ->addColumn('full_name', function ($student) {
                return $student->first_name . ' ' . $student->last_name;
            })
            ->addColumn('role_name', function ($student) {
                return $student->roles->pluck('name')->implode(', ');
            })
            ->addColumn('total_hours', function ($student) {
                return $student->studentAcademicHours->sum('hours_used') ?? 0;
            })
            ->addColumn('status_badge', function ($student) {
                $statusClass = $student->status === 'active' ? 'success' : 'danger';
                return '<span class="badge bg-' . $statusClass . '">' . ucfirst($student->status) . '</span>';
            })
            ->addColumn('actions', function ($student) {
                $actions = '<div class="btn-group" role="group">';
                
                if (_has_permission('students.view')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $student->id . '" title="View"><i class="fas fa-eye"></i></button>';
                }
                
                if (_has_permission('students.edit')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $student->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if (_has_permission('students.delete')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $student->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function searchStudents($query)
    {
        return User::where('user_type', 'student')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                  ->orWhere('last_name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get();
    }

    public function getStudentsByStatus($status)
    {
        return User::where('user_type', 'student')
            ->where('status', $status)
            ->get();
    }

    public function exportToExcel()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function exportToPDF()
    {
        $students = User::where('user_type', 'student')->get();
        $pdf = Pdf::loadView('exports.students-pdf', compact('students'));
        return $pdf->download('students.pdf');
    }

    public function getStudentStatistics()
    {
        return [
            'total' => User::where('user_type', 'student')->count(),
            'active' => User::where('user_type', 'student')->where('status', 'active')->count(),
            'inactive' => User::where('user_type', 'student')->where('status', 'inactive')->count(),
            'new_this_month' => User::where('user_type', 'student')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
}
