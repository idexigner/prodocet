<?php

namespace App\Repositories;

use App\Interfaces\LanguageInterface;
use App\Models\Language;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LanguagesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LanguageRepository implements LanguageInterface
{
    public function getAllLanguages(Request $request)
    {
        $query = Language::orderBy('name');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('native_name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function getLanguageById($id)
    {
        return Language::findOrFail($id);
    }

    public function createLanguage(array $data)
    {
        return Language::create($data);
    }

    public function updateLanguage($id, array $data)
    {
        $language = Language::findOrFail($id);
        $language->update($data);
        return $language;
    }

    public function deleteLanguage($id)
    {
        $language = Language::findOrFail($id);
        return $language->delete();
    }

    public function getLanguagesForDataTable(Request $request)
    {
        $query = Language::query();

        return DataTables::of($query)
            ->addColumn('status_badge', function ($language) {
                $statusClass = $language->status === 'active' ? 'success' : 'danger';
                return '<span class="badge bg-' . $statusClass . '">' . ucfirst($language->status) . '</span>';
            })
            ->addColumn('actions', function ($language) {
                $actions = '<div class="btn-group" role="group">';
                
                if (_has_permission('settings.view')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $language->id . '" title="View"><i class="fas fa-eye"></i></button>';
                }
                
                if (_has_permission('settings.edit')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $language->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if (_has_permission('settings.delete')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $language->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function searchLanguages($query)
    {
        return Language::where('name', 'like', '%' . $query . '%')
            ->orWhere('code', 'like', '%' . $query . '%')
            ->orWhere('native_name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();
    }

    public function getActiveLanguages()
    {
        return Language::where('status', 'active')->orderBy('name')->get();
    }

    public function exportToExcel()
    {
        return Excel::download(new LanguagesExport, 'languages.xlsx');
    }

    public function exportToPDF()
    {
        $languages = Language::all();
        $pdf = Pdf::loadView('exports.languages-pdf', compact('languages'));
        return $pdf->download('languages.pdf');
    }
}
