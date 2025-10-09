<?php

namespace App\Http\Controllers;

use App\Models\RateScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RateSchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $rateSchemes = RateScheme::withTrashed()
                    ->select(['id', 'letter_code', 'hourly_rate', 'description', 'created_at', 'deleted_at']);

                return DataTables::of($rateSchemes)
                    ->addColumn('hourly_rate_formatted', function ($rateScheme) {
                        return '$' . number_format($rateScheme->hourly_rate, 2);
                    })
                    ->addColumn('status', function ($rateScheme) {
                        if ($rateScheme->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return '<span class="badge bg-success">Active</span>';
                    })
                    ->addColumn('actions', function ($rateScheme) {
                        $actions = '';
                        
                        if (_has_permission('rate-schemes.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $rateScheme->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('rate-schemes.delete')) {
                            if ($rateScheme->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $rateScheme->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $rateScheme->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'actions'])
                    ->make(true);
            }

            return view('settings.rate-schemes.rate-schemes');

        } catch (\Exception $e) {
            _log_error('Failed to fetch rate schemes', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.fetch_failed')
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => __('settings.rate_schemes.messages.create_form_loaded')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'letter_code' => 'required|string|max:1|unique:rate_schemes,letter_code',
                'hourly_rate' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('settings.rate_schemes.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $rateScheme = RateScheme::create([
                'letter_code' => strtoupper($request->letter_code),
                'hourly_rate' => $request->hourly_rate,
                'description' => $request->description,
            ]);

            _log_info('Rate scheme created successfully', [
                'rate_scheme_id' => $rateScheme->id,
                'letter_code' => $rateScheme->letter_code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.rate_schemes.messages.created'),
                'data' => $rateScheme
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create rate scheme', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $rateScheme = RateScheme::withTrashed()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $rateScheme
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.not_found')
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $rateScheme = RateScheme::withTrashed()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $rateScheme
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.not_found')
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'letter_code' => 'required|string|max:1|unique:rate_schemes,letter_code,' . $id,
                'hourly_rate' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('settings.rate_schemes.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $rateScheme = RateScheme::findOrFail($id);
            $rateScheme->update([
                'letter_code' => strtoupper($request->letter_code),
                'hourly_rate' => $request->hourly_rate,
                'description' => $request->description,
            ]);

            _log_info('Rate scheme updated successfully', [
                'rate_scheme_id' => $rateScheme->id,
                'letter_code' => $rateScheme->letter_code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.rate_schemes.messages.updated'),
                'data' => $rateScheme
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update rate scheme', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $rateScheme = RateScheme::findOrFail($id);
            $rateScheme->delete();
            
            _log_info('Rate scheme deleted successfully', [
                'rate_scheme_id' => $rateScheme->id,
                'letter_code' => $rateScheme->letter_code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.rate_schemes.messages.deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete rate scheme', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            $rateScheme = RateScheme::withTrashed()->findOrFail($id);
            $rateScheme->restore();
            
            _log_info('Rate scheme restored successfully', [
                'rate_scheme_id' => $rateScheme->id,
                'letter_code' => $rateScheme->letter_code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.rate_schemes.messages.restored')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore rate scheme', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.rate_schemes.messages.restore_failed')
            ], 500);
        }
    }

    /**
     * Export rate schemes to Excel
     */
    public function exportExcel()
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export functionality']);
    }

    /**
     * Export rate schemes to PDF
     */
    public function exportPdf()
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export functionality']);
    }

    /**
     * Search rate schemes
     */
    public function search(Request $request)
    {
        // Implementation for search functionality
        return response()->json(['message' => 'Search functionality']);
    }
}