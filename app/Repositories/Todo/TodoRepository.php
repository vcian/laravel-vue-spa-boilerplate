<?php

namespace App\Repositories\Todo;

use App\Models\Todo\Todo;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Validator;

class TodoRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Todo::class;

    public function getTodoList()
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $query = Todo::whereUserId($user->id);

            if (request('show_todo_status') == '1') {
                $query->whereStatus(1);
            } else if (request('show_todo_status') == '0') {
                $query->whereStatus(0);
            }

            $todos = $query->get();

            return $todos;

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function storeTodo($input = null)
    {
        try {

            $validation = Validator::make($input, [
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

    public function todoStatus()
    {
        try {

            $todo = Todo::find(request('id'));

            $user = JWTAuth::parseToken()->authenticate();

            if (!$todo || $todo->user_id != $user->id) {
                return response()->json(['message' => 'Could not find todo!'], 422);
            }

            $todo->status = !$todo->status;
            $todo->save();

            return response()->json(['message' => 'Todo updated!']);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function deleteTodo($id = null)
    {
        try {

            $todo = Todo::find($id);

            $user = JWTAuth::parseToken()->authenticate();

            if (!$todo || $todo->user_id != $user->id) {
                return response()->json(['message' => 'Could not find todo!'], 422);
            }

            $todo->delete();

            return response()->json(['message' => 'Todo deleted!']);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }
}
