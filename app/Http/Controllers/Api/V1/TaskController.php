<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Validator;

class TaskController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'title'       => 'required|unique:tasks',
                'description' => 'required',
                'start_date'  => 'required|date_format:Y-m-d',
                'due_date'    => 'required|date_format:Y-m-d|after:start_date',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = \JWTAuth::parseToken()->authenticate();
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

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return response()->json(['message' => 'Couldnot find task!'], 422);
            }

            $task->delete();

            return response()->json(['message' => 'Task deleted!']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $task = Task::whereUuid($id)->first();

            if (!$task) {
                return response()->json(['message' => 'Couldnot find task!'], 422);
            }

            return $task;
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::whereUuid($id)->first();

            if (!$task) {
                return response()->json(['message' => 'Couldnot find task!']);
            }

            $validation = Validator::make($request->all(), [
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request)
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
