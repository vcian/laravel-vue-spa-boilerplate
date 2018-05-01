<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\Todo\TodoRepository;
use Illuminate\Http\Request;

/**
 * To Do Controller.
 */
class TodoController extends APIController
{
    /**
     * TodoRepository $todo
     *
     * @var object
     */
    protected $todo;

    /**
     * @param TodoRepository $todo
     */
    public function __construct(TodoRepository $todo)
    {
        $this->todo = $todo;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $todo = $this->todo->getTodoList();

        return $todo;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $tasks = $this->todo->storeTodo($input);

        return $tasks;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus()
    {
        $tasks = $this->todo->todoStatus();

        return $tasks;
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $tasks = $this->todo->deleteTodo($id);

        return $tasks;
    }
}
