<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEquipmentRequest;
use App\Http\Requests\Api\UpdateEquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Process;
use App\Models\Ubication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Equipment::with(['company', 'equipmentType', 'process', 'ubication']);

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('equipment_type_id')) {
            $query->where('equipment_type_id', $request->equipment_type_id);
        }

        $equipments = $query->paginate($request->get('per_page', 10));

        return EquipmentResource::collection($equipments);
    }

    public function store(StoreEquipmentRequest $request): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'tag' => $request->tag,
                'description' => $request->description,
                'company_id' => $request->company_id,
                'process_id' => $request->process_id,
                'ubication_id' => $request->ubication_id,
                'equipment_type_id' => $request->equipment_type_id,
                'status' => 'active',
            ];
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $data['image'] = $request->file('image')->store('images', 'public');
            }
            $equipment = Equipment::create($data);

            return response()->json([
                'message' => 'Se guardó el equipo correctamente',
                'data' => new EquipmentResource($equipment->load(['company', 'equipmentType', 'process', 'ubication']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar el equipo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Equipment $equipment): JsonResponse
    {
        return response()->json([
            'data' => new EquipmentResource($equipment->load(['company', 'equipmentType', 'process', 'ubication', 'components']))
        ]);
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'tag' => $request->tag,
                'description' => $request->description,
                'company_id' => $request->company_id,
                'process_id' => $request->process_id,
                'ubication_id' => $request->ubication_id,
                'equipment_type_id' => $request->equipment_type_id,
            ];
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($equipment->image) {
                    Storage::disk('public')->delete($equipment->image);
                }
                $data['image'] = $request->file('image')->store('images', 'public');
            }
            $equipment->update($data);

            return response()->json([
                'message' => 'Equipo actualizado correctamente',
                'data' => new EquipmentResource($equipment->load(['company', 'equipmentType', 'process', 'ubication']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el equipo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Equipment $equipment): JsonResponse
    {
        if (!auth()->user()->can('delete equipment')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $equipment->status = 'inactive';
            $equipment->save();
            $equipment->delete();

            return response()->json([
                'message' => 'El equipo se eliminó correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el equipo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEquipmentTypes(): JsonResponse
    {
        $types = EquipmentType::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $types]);
    }

    public function getProcesses(): JsonResponse
    {
        $processes = Process::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $processes]);
    }

    public function getUbications(): JsonResponse
    {
        $ubications = Ubication::where('status', 'active')
            ->select('id', 'name')
            ->get();

        return response()->json(['data' => $ubications]);
    }
}
