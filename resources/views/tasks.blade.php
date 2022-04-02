
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-success fade show" role="alert">
                        {{ session('status') }}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <div class="card card-new-task">
                    <div class="card-header">New Task</div>

                    <div class="card-body">
                        <form method="POST" id="addtask" action="{{ route('tasks.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input id="title" name="title" type="text" maxlength="255" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" autocomplete="off"/>
                                    <span class="invalid-feedback" role="alert">
                                @if ($errors->has('title'))
                                    <strong>{{ $errors->first('title') }}</strong>
                            
                                @endif    </span>
                            </div>
                            <button type="submit" id="sub" class="btn btn-primary">Create</button> <span class="loading" style="float:right"></span>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Tasks</div>

                    <div class="card-body" id="taskhere">
							@if (count($tasks)>0)
                        <table class="table table-striped" id="tasktable">
						  <thead>
							<tr>
						      <th width="55%">Task</th>
						      <th width="20%">Time</th>
						      <th width="25%">Process</th>
						    </tr>
					      </thead>
                            @foreach ($tasks as $task)
                                <tr id="task{{$task->id}}" 
									@if ($task->status==1)
									style="background-color:rgba(147, 250, 165, 1);"
									@elseif($task->status==2)
									style="background-color:rgba(214, 69, 65, 1);"
									@endif
									>
                                    <td>
                                        @if ($task->status>0)
                                            <s>{{ $task->title }}</s>
                                        @else
                                            {{ $task->title }}
                                        @endif
                                    </td>
									<td>{{Carbon\Carbon::parse($task->created_at)->diffForHumans()}}</td>
                                    <td class="text-right">
                                        @if ( $task->status==0)
												<style>
												
												</style>
                                                <span class="process-complete" data-id="{{$task->id}}" style="float:left;background-color:rgba(147, 250, 165, 1);margin:5px;cursor:pointer">&nbsp;&#x2714;&nbsp;</span>
                                                <span class="process-undone" data-id="{{$task->id}}" style="float:left;background-color:rgba(214, 69, 65, 1);margin:5px;cursor:pointer">&nbsp;&cross;&nbsp;</span>
                                                <span class="process-edit" data-id="{{$task->id}}" style="float:left;background-color:#3490dc;margin:5px;cursor:pointer"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;</span>
                                                <span class="process-delete" data-id="{{$task->id}}" style="float:left;background-color:#3490dc;margin:5px;cursor:pointer"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;</span>
                                            
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
						@else
							There is no tasks yet.
						@endif
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
	<style>
												.process-complete{float:left;background-color:rgba(147, 250, 165, 1);margin:5px;cursor:pointer}
												.process-undone{float:left;background-color:rgba(214, 69, 65, 1);margin:5px;cursor:pointer}
												.process-edit{float:left;background-color:#3490dc;margin:5px;cursor:pointer}
												.process-delete{float:left;background-color:#3490dc;margin:5px;cursor:pointer}
												</style>
	<script>
	function init(){
	
		$(".process-complete").on("click",function(t){
			let id= $(this).data("id"), e= $(this);
			if(confirm("Are you sure to mark as completed?")) {
				 $.ajax({url:'./ajax',type: "POST",data:{process:"complete",tid:id},dataType:'json',success: function(data){
					 if(data.status=="ok"){
						 $("#task"+id).find(">:first-child").html("<s>"+$("#task"+id).find(">:first-child").html()+"</s>");
						 $("#task"+data.last_id).css("background-color","rgba(147, 250, 165, 1)");
						 $("#task"+data.last_id+' .text-right').html("");
					$('.card-new-task').after(' <div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeIn();
					 }
				 }
					}); 
					}
		});
		$(".process-undone").click(function(t){
			let id= $(this).data("id"), e= $(this);
			if(confirm("Are you sure to mark as undone?")) {
				 $.ajax({url:'./ajax',type: "POST",data:{process:"undone",tid:id},dataType:'json',success: function(data){
					 if(data.status=="ok"){
						 $("#task"+id).find(">:first-child").html("<s>"+$("#task"+id).find(">:first-child").html()+"</s>");
						 $("#task"+data.last_id).css("background-color","rgba(214, 69, 65, 1)");
						 $("#task"+data.last_id+' .text-right').html("");
					$('.card-new-task').after(' <div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeIn();
					 }
				 }
					}); 
					}
		});
		$(".process-delete").click(function(t){
			let id= $(this).data("id");
			if( confirm("Are you sure to delete task?")){
				 $.ajax({url:'./ajax',type: "POST",data:{process:"delete",tid:id},dataType:'json',success: function(data){
						 $("#task"+data.last_id).fadeOut();
					$('.card-new-task').after(' <div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeIn();
				 }
					}); 
					}
		});
		$(".process-edit").click(function(t){
			let id= $(this).data("id"), ele=$("#task"+id).find(">:first-child");
			$("#task"+id+' .process-complete').hide();
			$("#task"+id+' .process-undone').hide();
			ele.html('<form id="taskform'+id+'" data-id="'+id+'"><input type="text" name="title" value="'+ele.html().trim()+'" maxlength="255" autocomplete="off"> <br> <button class="btn btn-primary">Save</button></form>');
			init();
		});
		$("form").submit(function(e){
			e.preventDefault();
			let id= $(this).data("id"),ele=$("#task"+id).find(">:first-child");
			
			 $.ajax({url:'./ajax',type: "POST",data:{process:"update",tid:id,title:$("#taskform"+id+" input[name=title]").val()},dataType:'json',success: function(data){
						ele.html(data.title);
					$('.card-new-task').after(' <div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeIn();
			$("#task"+data.last_id+' .process-complete').show();
			$("#task"+data.last_id+' .process-undone').show();
				 }
					}); 
		});
		
	
	}
	
	$(document).ready(function(){	
	$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
		init();
		
		$(".closealert").click(function(){
			// $(".alert").fadeOut();
			alert("someting");
		});
			$("#sub").click(function(e){
			e.preventDefault();
			if($("input[name=title]").val()!=""){
				$("input[name=title]").removeClass("is-invalid");
			$(".loading").html("<img src='{{ asset('loading.gif') }}' width='30'>");
				 $.ajax({url:'./ajax',type: "POST",data:$("#addtask").serialize(),dataType:'json',success: function(data){
					 if(data.status=="ok"){
						 if($("#tasktable").length>0){
							 $("thead").after('<tr style="background-color: rgba(255,255,159,.5);" id="task'+data.last_id+'"><td>'+data.title+'</td><td>just now</td><td class="text-right"><span class="process-complete" data-id="'+data.last_id+'" style="float:left;background-color:rgba(147, 250, 165, 1);margin:5px;cursor:pointer">&nbsp;&#x2714;&nbsp;</span><span class="process-undone" data-id="'+data.last_id+'" style="float:left;background-color:rgba(214, 69, 65, 1);margin:5px;cursor:pointer">&nbsp;&cross;&nbsp;</span><span class="process-edit" data-id="'+data.last_id+'" style="float:left;background-color:#3490dc;margin:5px;cursor:pointer"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;</span><span class="process-delete" data-id="'+data.last_id+'" style="float:left;background-color:#3490dc;margin:5px;cursor:pointer"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;</span></td></tr>');

						 }else{
							 $("#taskhere").html('<table class="table table-striped" id="tasktable"><thead><tr><th width="55%">Task</th><th width="20%">Time</th><th width="25%">Process</th></tr></thead><tr style="background-color: rgba(255,255,159,.5);" id="task'+data.last_id+'"><td>'+data.title+'</td><td>just now</td><td class="text-right"><span class="process-complete" data-id="'+data.last_id+'" style="float:left;background-color:rgba(147, 250, 165, 1);margin:5px;cursor:pointer">&nbsp;&#x2714;&nbsp;</span><span class="process-undone" data-id="'+data.last_id+'" style="float:left;background-color:rgba(214, 69, 65, 1);margin:5px;cursor:pointer">&nbsp;&cross;&nbsp;</span><span class="process-edit" data-id="'+data.last_id+'" style="float:left;background-color:#3490dc;margin:5px;cursor:pointer"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;</span><span class="process-delete" data-id="'+data.last_id+'" style="float:left;background-color:#3490dc;margin:5px;cursor:pointer"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;</span></td></tr></table>').fadeIn();
						 }
						 init();
						 $("input[name=title]").val('');
					$('.card-new-task').after(' <div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeIn();
						 
					$(".loading").html("");
					 }
					}
					}); 
			}
			else{
				$("input[name=title]").addClass("is-invalid");
				$(".invalid-feedback").html("<strong>Please fill title before saving.</strong>");
			
			}
		});
	});
	</script>
@endsection
