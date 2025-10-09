<?php

namespace App\Repositories;

use App\Interfaces\TeacherInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeachersExport;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherRepository implements TeacherInterface
{
    public function getAllTeachers(Request $request)
    {
        $query = User::where('user_type', 'teacher')
            ->with(['roles', 'groups'])
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

    public function getTeacherById($id)
    {
        return User::where('user_type', 'teacher')
            ->with(['roles', 'groups.course', 'groups.students'])
            ->findOrFail($id);
    }

    public function createTeacher(array $data)
    {
        DB::beginTransaction();
        try {
            $data['user_type'] = 'teacher';
            $data['password'] = bcrypt($data['password'] ?? 'password123');
            
            $teacher = User::create($data);
            
            // Assign teacher role if not already assigned
            if (!$teacher->hasRole('teacher')) {
                $teacher->assignRole('teacher');
            }

            DB::commit();
            return $teacher;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTeacher($id, array $data)
    {
        $teacher = User::where('user_type', 'teacher')->findOrFail($id);
        
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $teacher->update($data);
        return $teacher;
    }

    public function deleteTeacher($id)
    {
        $teacher = User::where('user_type', 'teacher')->findOrFail($id);
        return $teacher->delete();
    }

    public function restoreTeacher($id)
    {
        $teacher = User::where('user_type', 'teacher')->withTrashed()->findOrFail($id);
        return $teacher->restore();
    }

    public function getTeachersForDataTable(Request $request)
    {
        $query = User::where('user_type', 'teacher')
            ->with(['roles', 'groups']);

        return DataTables::of($query)
            ->addColumn('full_name', function ($teacher) {
                return $teacher->first_name . ' ' . $teacher->last_name;
            })
            ->addColumn('role_name', function ($teacher) {
                return $teacher->roles->pluck('name')->implode(', ');
            })
            ->addColumn('groups_count', function ($teacher) {
                return $teacher->groups->count();
            })
            ->addColumn('status_badge', function ($teacher) {
                $statusClass = $teacher->status === 'active' ? 'success' : 'danger';
                return '<span class="badge bg-' . $statusClass . '">' . ucfirst($teacher->status) . '</span>';
            })
            ->addColumn('actions', function ($teacher) {
                $actions = '<div class="btn-group" role="group">';
                
                if (_has_permission('teachers.view')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $teacher->id . '" title="View"><i class="fas fa-eye"></i></button>';
                }
                
                if (_has_permission('teachers.edit')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $teacher->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if (_has_permission('teachers.delete')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $teacher->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function searchTeachers($query)
    {
        return User::where('user_type', 'teacher')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                  ->orWhere('last_name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get();
    }

    public function getTeachersByStatus($status)
    {
        return User::where('user_type', 'teacher')
            ->where('status', $status)
            ->get();
    }

    public function exportToExcel()
    {
        return Excel::download(new TeachersExport, 'teachers.xlsx');
    }

    public function exportToPDF()
    {
        $teachers = User::where('user_type', 'teacher')->get();
        $pdf = Pdf::loadView('exports.teachers-pdf', compact('teachers'));
        return $pdf->download('teachers.pdf');
    }

    public function getTeacherStatistics()
    {
        return [
            'total' => User::where('user_type', 'teacher')->count(),
            'active' => User::where('user_type', 'teacher')->where('status', 'active')->count(),
            'inactive' => User::where('user_type', 'teacher')->where('status', 'inactive')->count(),
            'new_this_month' => User::where('user_type', 'teacher')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    public function getAvailableTeachers($date, $time)
    {
        // Get teachers who don't have classes at the specified date and time
        return User::where('user_type', 'teacher')
            ->where('status', 'active')
            ->whereDoesntHave('groups', function($query) use ($date, $time) {
                $query->whereHas('sessions', function($q) use ($date, $time) {
                    $q->where('session_date', $date)
                      ->where('start_time', $time);
                });
            })
            ->get();
    }
}
