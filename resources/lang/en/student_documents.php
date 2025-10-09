<?php

return [
    'title' => 'Student Documents',
    'heading' => 'Student Documents Management',
    
    // Document Types
    'document_types' => [
        'C.C.' => 'Citizenship Card',
        'C.E.' => 'Foreign ID Card',
        'T.I.' => 'Identity Card',
        'Pa.' => 'Passport',
        'Nit' => 'NIT',
        'other' => 'Other'
    ],
    
    // Form Labels
    'document_type' => 'Document Type',
    'document_number' => 'Document Number',
    'document_name' => 'Document Name',
    'is_primary' => 'Primary Document',
    'notes' => 'Notes',
    
    // Actions
    'add_document' => 'Add Document',
    'edit_document' => 'Edit Document',
    'delete_document' => 'Delete Document',
    'set_primary' => 'Set as Primary',
    
    // Messages
    'document_added' => 'Document added successfully',
    'document_updated' => 'Document updated successfully',
    'document_deleted' => 'Document deleted successfully',
    'document_set_primary' => 'Document set as primary successfully',
    'no_documents' => 'No documents found',
    'primary_document_required' => 'At least one primary document is required',
    'document_exists' => 'This document already exists for this student',
    
    // Validation
    'document_type_required' => 'Document type is required',
    'document_number_required' => 'Document number is required',
    'document_number_unique' => 'This document number already exists',
    'document_name_required_for_other' => 'Document name is required for "Other" type',
    
    // Placeholders
    'document_number_placeholder' => 'Enter document number',
    'document_name_placeholder' => 'Enter document name (for "Other" type)',
    'notes_placeholder' => 'Enter any additional notes',
    'select_document_type' => 'Select document type',
    
    // Status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'primary' => 'Primary',
    'secondary' => 'Secondary',
];
