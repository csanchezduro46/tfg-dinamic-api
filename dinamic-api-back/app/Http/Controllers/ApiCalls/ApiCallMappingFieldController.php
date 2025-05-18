<?php

namespace App\Http\Controllers\ApiCalls;

use App\Http\Controllers\Controller;
use App\Models\ApiCallMapping;
use App\Models\ApiCallMappingField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\RequestBody;

class ApiCallMappingFieldController extends Controller
{
    /**
     * @Endpoint(description: "Devuelve todos los campos mapeados de un mapeo de conexión")
     */
    public function getFieldsByMapping($mappingId)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        return response()->json($mapping->fields);
    }

    /**
     * @Endpoint(description: "Crea un nuevo campo mapeado dentro de un mapeo existente")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "source_field": "email",
     *         "target_field": "customer.email"
     *     }
     * )
     */
    public function store(Request $request, $mappingId)
    {
        $mapping = ApiCallMapping::findOrFail($mappingId);

        if ($mapping->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $validator = Validator::make($request->all(), [
            'source_field' => [
                'required',
                'string',
                'max:100',
                Rule::unique('api_call_mapping_fields')->where(function ($query) use ($mappingId, $request) {
                    return $query->where('api_call_mapping_id', $mappingId)
                        ->where('target_field', $request->input('target_field'));
                }),
            ],
            'target_field' => 'required|string|max:100',
        ], [
            'source_field.unique' => 'Ya existe un mapeo con estos campos para esta relación.',
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

    /**
     * @Endpoint(description: "Actualiza un campo mapeado dentro de un mapeo")
     * @RequestBody(
     *     content: "application/json",
     *     example: {
     *         "source_field": "nombre",
     *         "target_field": "customer.firstName"
     *     }
     * )
     */
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

    /**
     * @Endpoint(description: "Elimina un campo mapeado de un mapeo")
     */
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
