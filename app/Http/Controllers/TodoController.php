<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Todo::where('user_id', auth()->user()->id)->get();
        }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request-> validate ([
            'title' => 'required |string',
            'completed' => 'required|boolean',
            'inProgress' => 'required|boolean'
        ]);
        $todo = Todo:: create([
            'user_id' => auth()->user()->id,
            'title'=> $request->title,
            'completed' => $request->completed,
            'inProgress' => $request->inProgress,
        ]);
        return response($todo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        if($todo->user_id !== auth()->user()->id){
            return response()->json('Unauthorized', 401);
        }
        $data = $request-> validate ([
            'title' => 'required |string',
            'completed' => 'required|boolean',
            'inProgress' => 'required|boolean'
        ]);
        $todo->update($data);
        return response($todo, 200);
    }

    public function updateAll(Request $request){
        $data = $request->validate([
            'completed' => 'required|boolean',
        ]);

        Todo::where('user_id', auth()->user()->id)->update($data);

        return response("Updated", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        if($todo->user_id !== auth()->user()->id){
            return response()->json('Unauthorized', 401);
        }
        $todo->delete();
        return response("Deleted selected task", 200);
    }

    

    public function destroyCompleted(Request $request)
    {
        $todosToDelete = $request->todos;
        $userTodoIds = auth()->user()->todos->map(function ($todo){
            return $todo->id;
        });
        $valid = collect($todosToDelete)->every(function($value, $key) use ($userTodoIds){
            return $userTodoIds->contains($value);
        });
        if(!$valid){
            return response()->json('Unauthorized', 401);
        }
        $request->validate([
            'todos' => 'required|array',
        ]);
        Todo::destroy($request->todos);
        return response()->json("Deleted all completed tasks", 200);
    }

}
