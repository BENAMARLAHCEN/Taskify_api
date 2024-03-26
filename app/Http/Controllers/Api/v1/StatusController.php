<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    /**
     * Update the status of a task.
     *
     * @OA\Put(
     *     path="/api/v1/tasks/{task}/status",
     *     summary="Update task status",
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
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"To Do", "In Progress", "Done"})
     *         )
     *     ),
     *     @OA\Response(response="200", description="Task status updated successfully"),
     *     @OA\Response(response="403", description="Unauthorized"),
     *     @OA\Response(response="404", description="Task not found"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */

     
    public function __invoke(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:To Do,In Progress,Done'
        ]);

        $task->status = $request->status;
        $task->save();
        return response()->json(['message' => 'Task status updated successfully', 'task' => $task], 200);
    }
}
