<?php

namespace App\Repositories;

use App\Interfaces\CourseLevelInterface;
use App\Models\CourseLevel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CourseLevelsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseLevelRepository implements CourseLevelInterface
{
    public function getAllCourseLevels(Request $request)
    {
        $query = CourseLevel::with('language')->orderBy('name');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('language', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->has('language_id') && $request->language_id) {
            $query->where('language_id', $request->language_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function getCourseLevelById($id)
    {
        return CourseLevel::with('language')->findOrFail($id);
    }

    public function createCourseLevel(array $data)
    {
        return CourseLevel::create($data);
    }

    public function updateCourseLevel($id, array $data)
    {
        $courseLevel = CourseLevel::findOrFail($id);
        $courseLevel->update($data);
        return $courseLevel;
    }

    public function deleteCourseLevel($id)
    {
        $courseLevel = CourseLevel::findOrFail($id);
        return $courseLevel->delete();
    }

    public function getCourseLevelsForDataTable(Request $request)
    {
        $query = CourseLevel::with('language');

        return DataTables::of($query)
            ->addColumn('language_name', function ($courseLevel) {
                return $courseLevel->language->name ?? 'N/A';
            })
            ->addColumn('status_badge', function ($courseLevel) {
                $statusClass = $courseLevel->status === 'active' ? 'success' : 'danger';
                return '<span class="badge bg-' . $statusClass . '">' . ucfirst($courseLevel->status) . '</span>';
            })
            ->addColumn('actions', function ($courseLevel) {
                $actions = '<div class="btn-group" role="group">';
                
                if (_has_permission('settings.view')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $courseLevel->id . '" title="View"><i class="fas fa-eye"></i></button>';
                }
                
                if (_has_permission('settings.edit')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $courseLevel->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if (_has_permission('settings.delete')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $courseLevel->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function searchCourseLevels($query)
    {
        return CourseLevel::with('language')
            ->where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->orWhereHas('language', function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get();
    }

    public function getActiveCourseLevels()
    {
        return CourseLevel::where('status', 'active')
            ->with('language')
            ->orderBy('name')
            ->get();
    }

    public function getCourseLevelsByLanguage($languageId)
    {
        return CourseLevel::where('language_id', $languageId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function exportToExcel()
    {
        return Excel::download(new CourseLevelsExport, 'course-levels.xlsx');
    }

    public function exportToPDF()
    {
        $courseLevels = CourseLevel::with('language')->get();
        $pdf = Pdf::loadView('exports.course-levels-pdf', compact('courseLevels'));
        return $pdf->download('course-levels.pdf');
    }
}
