<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AjaxController extends Controller
{
		public function index(Request $request)
			{

				if(!isset($request->process)):
		  $data = $this->validate($request, [
            'title' => 'required|string|max:255',
        ]);
       $save= Auth::user()->tasks()->create([
            'title' => $data['title'],
            'status' => false,
        ]);
				return response()->json([
					'status' => 'ok',	
					'message' => "Task Created!",
					'title' => $data['title'],
					'last_id' => $save->id
				], 200);
				else:
					if($request->process=="complete"):
					
						$task = Auth::user()->tasks()->find($request->tid);
						$task->status = 1;
						$task->save();
						return response()->json([
					'status' => 'ok',	
					'message' => "Task Completed!",
					'last_id' => $request->tid
				], 200);
					elseif($request->process=="undone"):
					
						$task = Auth::user()->tasks()->find($request->tid);
						$task->status = 2;
						$task->save();
						return response()->json([
					'status' => 'ok',	
					'message' => "Task marked as undone!",
					'last_id' => $request->tid
				], 200);
					
					elseif($request->process=="delete"):
						$task = Auth::user()->tasks()->where("id",$request->tid);
					
						$task->delete();
						return response()->json([
					'status' => 'ok',	
					'message' => "Task Deleted!",
					'last_id' => $request->tid
				], 200);
				
					elseif($request->process=="update"):
						 $data = $this->validate($request, [
            'title' => 'required|string|max:255',
        ]);
						$task = Auth::user()->tasks()->where("id",$request->tid)->update(["title"=>$request->title]);
				return response()->json([
					'status' => 'ok',	
					'message' => "Task updated!",
					'title' => $request->title,
					'last_id' => $request->tid
				], 200);
					endif;
				endif;
				
			}
		
}
