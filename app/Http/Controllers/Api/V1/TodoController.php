<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Todo\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Validator;

/**
 * To Do Controller.
 */
class TodoController extends APIController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $query = Todo::whereUserId($user->id);

            if (request()->has('show_todo_status')) {
                $query->whereStatus(request('show_todo_status'));
            }

            $todos = $query->get();

            return $todos;
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
                'todo' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $todo = new Todo();
            $todo->fill(request()->all());
            $todo->user_id = $user->id;
            $todo->save();

            return response()->json(['message' => 'Todo added!', 'data' => $todo]);
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
            $todo = Todo::find(request('id'));
            $user = JWTAuth::parseToken()->authenticate();

            if (!$todo || $todo->user_id != $user->id) {
                return response()->json(['message' => 'Couldnot find todo!'], 422);
            }

            $todo->status = !$todo->status;
            $todo->save();

            return response()->json(['message' => 'Todo updated!']);
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
            $todo = Todo::find($id);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$todo || $todo->user_id != $user->id) {
                return response()->json(['message' => 'Couldnot find todo!'], 422);
            }

            $todo->delete();

            return response()->json(['message' => 'Todo deleted!']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }
}
