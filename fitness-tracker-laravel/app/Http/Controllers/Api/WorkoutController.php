<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkoutController extends Controller
{
    /**
     * Get all workouts for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $workouts = $request->user()
            ->workouts()
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'workouts' => $workouts,
        ]);
    }

    /**
     * Get single workout
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $workout = $request->user()
            ->workouts()
            ->findOrFail($id);

        return response()->json($workout);
    }

    /**
     * Create new workout
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workout = $request->user()->workouts()->create($validated);

        return response()->json([
            'message' => 'Workout created successfully',
            'workout' => $workout,
        ], 201);
    }

    /**
     * Update workout
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $workout = $request->user()
            ->workouts()
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workout->update($validated);

        return response()->json([
            'message' => 'Workout updated successfully',
            'workout' => $workout,
        ]);
    }

    /**
     * Delete workout
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $workout = $request->user()
            ->workouts()
            ->findOrFail($id);

        $workout->delete();

        return response()->json([
            'message' => 'Workout deleted successfully',
        ]);
    }
}

