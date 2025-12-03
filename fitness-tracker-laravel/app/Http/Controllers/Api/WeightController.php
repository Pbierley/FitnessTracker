<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeightTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeightController extends Controller
{
    /**
     * Get all weight entries for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->weightTracking();

        if ($request->has('start_date')) {
            $query->where('weight_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('weight_date', '<=', $request->end_date);
        }

        $weights = $query->orderBy('weight_date', 'desc')->get();

        return response()->json([
            'weights' => $weights,
        ]);
    }

    /**
     * Get single weight entry
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $weight = $request->user()
            ->weightTracking()
            ->findOrFail($id);

        return response()->json($weight);
    }

    /**
     * Create new weight entry
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'weight' => 'required|numeric|gt:0',
            'weight_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $weight = $request->user()->weightTracking()->create([
                'weight' => $validated['weight'],
                'weight_date' => $validated['weight_date'] ?? now()->format('Y-m-d'),
                'notes' => $validated['notes'] ?? '',
            ]);

            return response()->json([
                'message' => 'Weight entry added successfully',
                'weight' => $weight,
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate date entry
            if ($e->getCode() == 23000) {
                return response()->json([
                    'error' => 'Weight entry already exists for this date',
                ], 409);
            }
            throw $e;
        }
    }

    /**
     * Update weight entry
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $weight = $request->user()
            ->weightTracking()
            ->findOrFail($id);

        $validated = $request->validate([
            'weight' => 'nullable|numeric|gt:0',
            'weight_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $updateData = ['notes' => $validated['notes'] ?? ''];

            if (isset($validated['weight'])) {
                $updateData['weight'] = $validated['weight'];
            }

            if (isset($validated['weight_date'])) {
                $updateData['weight_date'] = $validated['weight_date'];
            }

            $weight->update($updateData);

            return response()->json([
                'message' => 'Weight entry updated successfully',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate date entry
            if ($e->getCode() == 23000) {
                return response()->json([
                    'error' => 'Weight entry already exists for this date',
                ], 409);
            }
            throw $e;
        }
    }

    /**
     * Delete weight entry
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $weight = $request->user()
            ->weightTracking()
            ->findOrFail($id);

        $weight->delete();

        return response()->json([
            'message' => 'Weight entry deleted successfully',
        ]);
    }
}

