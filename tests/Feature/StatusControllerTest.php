<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatusControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test updating the status of a task.
     *
     * @return void
     */
    public function testUpdateTaskStatus()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'In Progress',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task status updated successfully',
                'task' => [
                    'id' => $task->id,
                    'status' => 'In Progress',
                ],
            ]);
    }

    /**
     * Test updating the status of a task with an invalid status.
     *
     * @return void
     */
    public function testUpdateTaskStatusWithInvalidStatus()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'Invalid Status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'status' => 'The selected status is invalid.',
            ]);
    }

    /**
     * Test updating the status of a task by a different user (unauthorized).
     *
     * @return void
     */
    public function testUpdateTaskStatusUnauthorized()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->putJson("/api/v1/tasks/{$task->id}/status", [
            'status' => 'In Progress',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
