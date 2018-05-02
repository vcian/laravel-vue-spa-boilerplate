<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\Task\TaskRepository;
use Illuminate\Http\Request;

/**
 * Task Controller.
 */
class TaskController extends APIController
{
    /**
     * TaskRepository $task
     *
     * @var object
     */
    protected $task;

    /**
     * @param TaskRepository $task
     */
    public function __construct(TaskRepository $task)
    {
        $this->task = $task;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tasks = $this->task->getTasks();

        return $tasks;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $tasks = $this->task->storeTask($input);

        return $tasks;
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $tasks = $this->task->deleteTask($id);

        return $tasks;
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tasks = $this->task->showTask($id);

        return $tasks;
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $tasks = $this->task->updateTask($input, $id);

        return $tasks;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request)
    {
        $tasks = $this->task->taskStatus($request);

        return $tasks;
    }
}
