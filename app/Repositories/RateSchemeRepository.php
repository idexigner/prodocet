<?php

namespace App\Repositories;

use App\Interfaces\RateSchemeInterface;
use App\Models\RateScheme;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RateSchemesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class RateSchemeRepository implements RateSchemeInterface
{
    public function getAllRateSchemes(Request $request)
    {
        $query = RateScheme::orderBy('letter_code');

        if ($request->has('search') && $request->search) {
            $query->where('letter_code', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function getRateSchemeById($id)
    {
        return RateScheme::findOrFail($id);
    }

    public function createRateScheme(array $data)
    {
        return RateScheme::create($data);
    }

    public function updateRateScheme($id, array $data)
    {
        $rateScheme = RateScheme::findOrFail($id);
        $rateScheme->update($data);
        return $rateScheme;
    }

    public function deleteRateScheme($id)
    {
        $rateScheme = RateScheme::findOrFail($id);
        return $rateScheme->delete();
    }

    public function getRateSchemesForDataTable(Request $request)
    {
        $query = RateScheme::query();

        return DataTables::of($query)
            ->addColumn('hourly_rate_formatted', function ($rateScheme) {
                return '$' . number_format($rateScheme->hourly_rate, 2);
            })
            ->addColumn('actions', function ($rateScheme) {
                $actions = '<div class="btn-group" role="group">';
                
                if (_has_permission('settings.view')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-id="' . $rateScheme->id . '" title="View"><i class="fas fa-eye"></i></button>';
                }
                
                if (_has_permission('settings.edit')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $rateScheme->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                }
                
                if (_has_permission('settings.delete')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $rateScheme->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function searchRateSchemes($query)
    {
        return RateScheme::where('letter_code', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();
    }

    public function getRateSchemeByLetterCode($letterCode)
    {
        return RateScheme::where('letter_code', $letterCode)->first();
    }

    public function exportToExcel()
    {
        return Excel::download(new RateSchemesExport, 'rate-schemes.xlsx');
    }

    public function exportToPDF()
    {
        $rateSchemes = RateScheme::all();
        $pdf = Pdf::loadView('exports.rate-schemes-pdf', compact('rateSchemes'));
        return $pdf->download('rate-schemes.pdf');
    }
}
