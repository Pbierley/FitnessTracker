<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Get all sessions for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()
            ->workoutSessions()
            ->with(['workout:id,name', 'sets' => function ($query) {
                $query->orderBy('set_number', 'asc');
            }])
            ->withCount('sets')
            ->withSum('sets as total_reps', 'reps');

        if ($request->has('workout_id')) {
            $query->where('workout_id', $request->workout_id);
        }

        $sessions = $query->orderBy('session_date', 'desc')->get();

        // Format the response to match old API
        $formattedSessions = $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'workout_id' => $session->workout_id,
                'session_date' => $session->session_date->format('Y-m-d'),
                'notes' => $session->notes,
                'workout_name' => $session->workout->name,
                'total_sets' => $session->sets_count,
                'total_reps' => $session->total_reps ?? 0,
                'sets' => $session->sets->map(function ($set) {
                    return [
                        'set_number' => $set->set_number,
                        'reps' => $set->reps,
                        'weight' => $set->weight,
                    ];
                }),
            ];
        });

        return response()->json([
            'sessions' => $formattedSessions,
        ]);
    }

    /**
     * Get single session with sets
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $session = $request->user()
            ->workoutSessions()
            ->with(['workout:id,name', 'sets' => function ($query) {
                $query->orderBy('set_number', 'asc');
            }])
            ->findOrFail($id);

        return response()->json([
            'id' => $session->id,
            'workout_id' => $session->workout_id,
            'session_date' => $session->session_date->format('Y-m-d'),
            'notes' => $session->notes,
            'workout_name' => $session->workout->name,
            'sets' => $session->sets,
        ]);
    }

    /**
     * Create new session with sets
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'session_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'sets' => 'nullable|array',
            'sets.*.set_number' => 'nullable|integer',
            'sets.*.reps' => 'required|integer|min:0',
            'sets.*.weight' => 'required|numeric|min:0',
        ]);

        // Verify workout ownership
        $workout = $request->user()->workouts()->findOrFail($validated['workout_id']);

        return DB::transaction(function () use ($request, $validated) {
            $session = $request->user()->workoutSessions()->create([
                'workout_id' => $validated['workout_id'],
                'session_date' => $validated['session_date'] ?? now()->format('Y-m-d'),
                'notes' => $validated['notes'] ?? '',
            ]);

            if (!empty($validated['sets'])) {
                foreach ($validated['sets'] as $index => $set) {
                    $session->sets()->create([
                        'set_number' => $set['set_number'] ?? ($index + 1),
                        'reps' => $set['reps'],
                        'weight' => $set['weight'],
                    ]);
                }
            }

            return response()->json([
                'message' => 'Session created successfully',
                'session' => [
                    'id' => $session->id,
                    'workout_id' => $session->workout_id,
                    'session_date' => $session->session_date->format('Y-m-d'),
                    'notes' => $session->notes,
                ],
            ], 201);
        });
    }

    /**
     * Update session
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $session = $request->user()
            ->workoutSessions()
            ->findOrFail($id);

        $validated = $request->validate([
            'session_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'sets' => 'nullable|array',
            'sets.*.set_number' => 'nullable|integer',
            'sets.*.reps' => 'required|integer|min:0',
            'sets.*.weight' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($session, $validated) {
            $updateData = ['notes' => $validated['notes'] ?? ''];
            
            if (isset($validated['session_date'])) {
                $updateData['session_date'] = $validated['session_date'];
            }

            $session->update($updateData);

            if (isset($validated['sets'])) {
                // Delete existing sets
                $session->sets()->delete();

                // Add new sets
                if (!empty($validated['sets'])) {
                    foreach ($validated['sets'] as $index => $set) {
                        $session->sets()->create([
                            'set_number' => $set['set_number'] ?? ($index + 1),
                            'reps' => $set['reps'],
                            'weight' => $set['weight'],
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Session updated successfully',
            ]);
        });
    }

    /**
     * Delete session
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $session = $request->user()
            ->workoutSessions()
            ->findOrFail($id);

        $session->delete();

        return response()->json([
            'message' => 'Session deleted successfully',
        ]);
    }
}

