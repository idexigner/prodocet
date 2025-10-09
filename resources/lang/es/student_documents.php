<?php

return [
    'title' => 'Documentos del Estudiante',
    'heading' => 'Gestión de Documentos del Estudiante',
    
    // Document Types
    'document_types' => [
        'C.C.' => 'Cédula de Ciudadanía',
        'C.E.' => 'Cédula de Extranjería',
        'T.I.' => 'Tarjeta de Identidad',
        'Pa.' => 'Pasaporte',
        'Nit' => 'NIT',
        'other' => 'Otro'
    ],
    
    // Form Labels
    'document_type' => 'Tipo de Documento',
    'document_number' => 'Número de Documento',
    'document_name' => 'Nombre del Documento',
    'is_primary' => 'Documento Principal',
    'notes' => 'Notas',
    
    // Actions
    'add_document' => 'Agregar Documento',
    'edit_document' => 'Editar Documento',
    'delete_document' => 'Eliminar Documento',
    'set_primary' => 'Establecer como Principal',
    
    // Messages
    'document_added' => 'Documento agregado exitosamente',
    'document_updated' => 'Documento actualizado exitosamente',
    'document_deleted' => 'Documento eliminado exitosamente',
    'document_set_primary' => 'Documento establecido como principal exitosamente',
    'no_documents' => 'No se encontraron documentos',
    'primary_document_required' => 'Se requiere al menos un documento principal',
    'document_exists' => 'Este documento ya existe para este estudiante',
    
    // Validation
    'document_type_required' => 'El tipo de documento es requerido',
    'document_number_required' => 'El número de documento es requerido',
    'document_number_unique' => 'Este número de documento ya existe',
    'document_name_required_for_other' => 'El nombre del documento es requerido para el tipo "Otro"',
    
    // Placeholders
    'document_number_placeholder' => 'Ingrese el número de documento',
    'document_name_placeholder' => 'Ingrese el nombre del documento (para tipo "Otro")',
    'notes_placeholder' => 'Ingrese notas adicionales',
    'select_document_type' => 'Seleccione el tipo de documento',
    
    // Status
    'active' => 'Activo',
    'inactive' => 'Inactivo',
    'primary' => 'Principal',
    'secondary' => 'Secundario',
];
