<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 10);

        $tasks = $request->query('getWithProject') === 'true'
            ? Task::with('project')
            : Task::query();

        if ($request->query('search')) {
            $search = $request->query('search');
            $tasks->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->query('priority_level')) {
            $priorityLevels = explode(',', $request->query('priority_level'));
            $tasks->whereIn('priority_level', $priorityLevels);
        }

        if ($request->query('status')) {
            $statuses = explode(',', $request->query('status'));
            $tasks->whereIn('status', $statuses);
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $tasks->orderBy($sortBy, $sortDirection);

        $tasks = $tasks->paginate($perPage);

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found matching the criteria',
                'tasks' => []
            ], 404);
        }

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority_level' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:active,on_hold,completed',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        try {
            $validated['description'] = $validated['description'] ?? 'No description provided.';
            $validated['start_time'] = $validated['start_time'] ?? now();
            $validated['end_time'] = $validated['end_time'] ?? null;

            DB::beginTransaction();

            $newTask = Task::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Task created successfully!',
                'task' => $newTask
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Task creation failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Task $task, Request $request)
    {
        if ($request->has('with')) {
            $relationships = explode(',', $request->query('with'));
            $task->load($relationships);
        }

        return response()->json([
            'message' => 'Task retrieved successfully',
            'task' => $task,
            'timestamp' => now()
        ], 200);
    }
    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return response()->json($task, 200);
    }
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}
