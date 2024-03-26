<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     summary="Get all tasks",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(response="200", description="List of tasks"),
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('viewAny', Task::class);
        $tasks = Task::latest()->where('user_id', $user->id)->get();
        return response()->json(['tasks'=>$tasks]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Task Title"),
     *             @OA\Property(property="description", type="string", example="Task Description")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Task created successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = new Task();
        $task->user_id = Auth::id();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->save();

        return response()->json($task, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{task}",
     *     summary="Get a specific task",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Task details"),
     *     @OA\Response(response="404", description="Task not found")
     * )
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json($task);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tasks/{task}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Task Title"),
     *             @OA\Property(property="description", type="string", example="Updated Task Description")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Task updated successfully"),
     *     @OA\Response(response="404", description="Task not found"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function update(Request $request, Task $task)
    {

        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->save();

        return response()->json($task);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/tasks/{task}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Task deleted successfully"),
     *     @OA\Response(response="404", description="Task not found")
     * )
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
