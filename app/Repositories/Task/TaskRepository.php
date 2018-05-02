<?php

namespace App\Repositories\Task;

use App\Models\Task\Task;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Validator;

class TaskRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Task::class;

    public function getTasks()
    {
        try {

            $tasks = Task::whereNotNull('id');

            if (request()->has('title')) {
                $tasks->where('title', 'like', '%'.request('title').'%');
            }

            if (request()->has('status')) {
                $tasks->whereStatus(request('status'));
            }

            $tasks->orderBy(request('sortBy'), request('order'));

            return $tasks->paginate(request('pageLength'));

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function storeTask($input = null)
    {
        try {

            $validation = Validator::make($input, [
                'title'       => 'required|unique:tasks',
                'description' => 'required',
                'start_date'  => 'required|date_format:Y-m-d',
                'due_date'    => 'required|date_format:Y-m-d|after:start_date',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $task = new Task();
            $task->fill(request()->all());
            $task->uuid = generateUuid();
            $task->user_id = $user->id;
            $task->save();

            return response()->json(['message' => 'Task added!', 'data' => $task]);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function deleteTask($id = null)
    {
        try {

            $task = Task::find($id);

            if (!$task) {
                return response()->json(['message' => 'Could not find task!'], 422);
            }

            $task->delete();

            return response()->json(['message' => 'Task deleted!']);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function showTask($id = null)
    {
        try {

            $task = Task::whereUuid($id)->first();

            if (!$task) {
                return response()->json(['message' => 'Could not find task!'], 422);
            }

            return $task;
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function updateTask($input = null, $id = null)
    {
        try {

            $task = Task::whereUuid($id)->first();

            if (!$task) {
                return response()->json(['message' => 'Couldnot find task!']);
            }

            $validation = Validator::make($input, [
                'title'       => 'required|unique:tasks,title,'.$task->id.',id',
                'description' => 'required',
                'start_date'  => 'required|date_format:Y-m-d',
                'due_date'    => 'required|date_format:Y-m-d|after:start_date',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $task->title = request('title');
            $task->description = request('description');
            $task->start_date = request('start_date');
            $task->due_date = request('due_date');
            $task->progress = request('progress');
            $task->save();

            return response()->json(['message' => 'Task updated!', 'data' => $task]);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function taskStatus($request = null)
    {
        try {

            $task = Task::find($request->input('id'));

            if (!$task) {
                return response()->json(['message' => 'Couldnot find task!'], 422);
            }

            $task->status = !$task->status;
            $task->save();

            return response()->json(['message' => 'Task updated!']);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }
}