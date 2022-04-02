<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Auth::user()
            ->tasks()
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('tasks', [
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|string|max:255',
        ]);
        Auth::user()->tasks()->create([
            'title' => $data['title'],
            'status' => 0,
        ]);
        session()->flash('status', 'Task Created!');

        return redirect('/tasks');
    }

    public function update(Task $task)
    {
        $this->authorize('complete', $task);
        $task->is_complete = true;
        $task->save();
        session()->flash('status', 'Task Completed!');
        return redirect('/tasks');
    }
}
