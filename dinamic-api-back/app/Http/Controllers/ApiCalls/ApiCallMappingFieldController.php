<?php

namespace App\Http\Controllers\ApiCalls;

use App\Http\Controllers\Controller;
use App\Models\ApiCallMapping;
use App\Models\ApiCallMappingField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiCallMappingFieldController extends Controller
{
    public function getFieldsByMapping($mappingId)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        return response()->json($mapping->fields);
    }

    public function store(Request $request, $mappingId)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $rules = [
            'source_field' => 'required|string|max:100',
            'target_field' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'source_field.required' => 'El campo de origen es obligatorio.',
            'target_field.required' => 'El campo de destino es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $field = $mapping->fields()->create($validator->validated());

        return response()->json([
            'msg' => 'Se ha creado el mapeo de campos correctamente.',
            'field' => $field
        ], 201);
    }

    public function update(Request $request, $mappingId, $fieldId)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);
        $field = ApiCallMappingField::where('api_call_mapping_id', $mappingId)->findOrFail($fieldId);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $validator = Validator::make($request->all(), [
            'source_field' => 'sometimes|string|max:100',
            'target_field' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $field->update($validator->validated());

        return response()->json([
            'msg' => 'Se ha actualizado el mapeo de campos correctamente.',
            'field' => $field
        ]);
    }

    public function delete($mappingId, $fieldId)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);
        $field = ApiCallMappingField::where('api_call_mapping_id', $mappingId)->findOrFail($fieldId);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $field->delete();

        return response()->json([
            'msg' => 'Mapeo de campos eliminado correctamente.'
        ]);
    }
}
