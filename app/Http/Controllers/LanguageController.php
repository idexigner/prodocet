<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $languages = Language::withTrashed()
                    ->select(['id', 'code', 'name', 'native_name', 'is_active', 'created_at', 'deleted_at']);

                return DataTables::of($languages)
                    ->addColumn('status', function ($language) {
                        if ($language->deleted_at) {
                            return '<span class="badge bg-danger">Deleted</span>';
                        }
                        return $language->is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>';
                    })
                    ->addColumn('actions', function ($language) {
                        $actions = '';
                        
                        if (_has_permission('languages.edit')) {
                            $actions .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $language->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button> ';
                        }
                        
                        if (_has_permission('languages.delete')) {
                            if ($language->deleted_at) {
                                $actions .= '<button class="btn btn-sm btn-success restore-btn" data-id="' . $language->id . '" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>';
                            } else {
                                $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $language->id . '" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>';
                            }
                        }
                        
                        return $actions;
                    })
                    ->rawColumns(['status', 'actions'])
                    ->make(true);
            }

            return view('settings.languages.languages');

        } catch (\Exception $e) {
            _log_error('Failed to fetch languages', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.fetch_failed')
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
            'message' => __('settings.languages.messages.create_form_loaded')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:5|unique:languages,code',
                'name' => 'required|string|max:255',
                'native_name' => 'required|string|max:255',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('settings.languages.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $language = Language::create([
                'code' => strtolower($request->code),
                'name' => $request->name,
                'native_name' => $request->native_name,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Language created successfully', [
                'language_id' => $language->id,
                'code' => $language->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.languages.messages.created'),
                'data' => $language
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to create language', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.create_failed')
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $language = Language::withTrashed()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $language
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.not_found')
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $language = Language::withTrashed()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $language
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.not_found')
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
                'code' => 'required|string|max:5|unique:languages,code,' . $id,
                'name' => 'required|string|max:255',
                'native_name' => 'required|string|max:255',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('settings.languages.messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $language = Language::findOrFail($id);
            $language->update([
                'code' => strtolower($request->code),
                'name' => $request->name,
                'native_name' => $request->native_name,
                'is_active' => $request->boolean('is_active', true)
            ]);

            _log_info('Language updated successfully', [
                'language_id' => $language->id,
                'code' => $language->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.languages.messages.updated'),
                'data' => $language
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to update language', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.update_failed')
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $language = Language::findOrFail($id);
            $language->delete();
            
            _log_info('Language deleted successfully', [
                'language_id' => $language->id,
                'code' => $language->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.languages.messages.deleted')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to delete language', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.delete_failed')
            ], 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        try {
            $language = Language::withTrashed()->findOrFail($id);
            $language->restore();
            
            _log_info('Language restored successfully', [
                'language_id' => $language->id,
                'code' => $language->code,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('settings.languages.messages.restored')
            ]);

        } catch (\Exception $e) {
            _log_error('Failed to restore language', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('settings.languages.messages.restore_failed')
            ], 500);
        }
    }

    /**
     * Change language for the application
     */
    public function changeLanguage(Request $request, $locale)
    {
        // Validate the locale parameter
        if (!in_array($locale, ['en', 'es'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language code'
            ], 422);
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return response()->json([
            'success' => true,
            'message' => __('common.language_changed'),
            'language' => $locale
        ]);
    }

    /**
     * Export languages to Excel
     */
    public function exportExcel()
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export functionality']);
    }

    /**
     * Export languages to PDF
     */
    public function exportPdf()
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export functionality']);
    }

    /**
     * Search languages
     */
    public function search(Request $request)
    {
        // Implementation for search functionality
        return response()->json(['message' => 'Search functionality']);
    }
}