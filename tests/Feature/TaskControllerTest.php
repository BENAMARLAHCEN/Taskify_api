<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting tasks.
     *
     * @return void
     */
    public function testGetTasks()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test creating a task.
     *
     * @return void
     */
    public function testCreateTask()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Task',
                'description' => 'This is a test task',
            ]);
    }

    /**
     * Test getting a single task.
     *
     * @return void
     */
    public function testGetTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
            ]);
    }

    /**
     * Test updating a task.
     *
     * @return void
     */
    public function testUpdateTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'This is an updated task',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Task',
                'description' => 'This is an updated task',
            ]);
    }

    /**
     * Test deleting a task.
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted successfully',
            ]);

            $this->assertDatabaseMissing('tasks', [
                'id' => $task->id,
            ]);
    }
}
