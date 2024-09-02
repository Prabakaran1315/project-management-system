<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\Project;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:task_read'])->only(['taskList', 'getTaskById']);
        $this->middleware(['permission:task_update'])->only(['updateProjectById']);
        $this->middleware(['permission:task_create'])->only(['store']);
        $this->middleware(['permission:task_delete'])->only(['deleteTaskById']);
        $this->middleware(['permission:task_status_update'])->only(['deleteProjectById']);
    }

    public function taskList(Request $request){

        if($request->ajax()){
            $search =$request->search['value'];
            $query = Task::select('tasks.*', 'projects.name as project_name', 'users.name as user_name')
                        ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
                        ->leftJoin('users', 'users.id', '=', 'tasks.user_id')
                        ->when(auth()->user()->hasRole('Team Member'), function ($query) {
                            $query->where('users.id', auth()->id());
                        })
                        ->when($search, function ($query) use ($search) {
                            $query->where('tasks.title', 'like', "%$search%");
                            $query->orWhere('projects.name', 'like', "%$search%");
                        });

            if ($request->has('order.0.column') && $request->has('order.0.dir')) {
                $orderBy = $request->columns[$request->input('order.0.column')]['data'];
                $orderDirection = $request->input('order.0.dir');
                $query->orderBy($orderBy, $orderDirection);
            }else{
                $query->orderBy('created_at', 'desc');
            }

            $totalRecords = $query->count();
            $start = $request->input('start');
            $length = $request->input('length');
            $query->skip($start)->take($length);
            $data = $query->get();

            $data->transform(function ($row) {
                $buttons = '';
               
                if (auth()->user()->can('task_read')) {
                    $viewButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" id="viewTask" class="text-blue-500 hover:text-blue-700 mx-1">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>';
                    $buttons .= $viewButton;
                }
                
                if (auth()->user()->can('task_update')) {
                    $editButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" id="editTask" class="text-yellow-500 hover:text-yellow-700 mx-1">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>';
                    $buttons .= $editButton;
                }
                
                if (auth()->user()->can('task_delete')) {
                    $deleteButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" id="deleteTask" class="text-red-500 hover:text-red-700 mx-1">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>';
                    $buttons .= $deleteButton;
                }
                

                $row->action = $buttons;
                return $row;
            });

            return response()->json([
                'success' => true,
                'message' => 'Task retrieved successfully.',
                'data' => $data,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
            ], 200);
        }

        $projects = Project::all();
        return view('tasks', compact('projects'));
    }

    public function store(TaskRequest $request)
    {
        try {

            $validatedData = $request->validated();
            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'dead_line' => $validatedData['dead_line'],
                'project_id' => $validatedData['project_id'],
                'user_id' => $validatedData['assignee'],
                'status' => 1 // Created
            ]);

            return ResponseHelper::success(Constants::SUCCESS_CREATED, $task, Response::HTTP_CREATED);

        } catch (Exception $e) {
            \Log::error('Task creation failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());   

        }
    }

    public function getTaskById($id){
        $task = Task::select('tasks.*', 'projects.name as project_name')->where('tasks.id', $id)->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')->first();
        $task->status_info;
        return ResponseHelper::success(Constants::SUCCESS_DATA_FETCHED, $task, Response::HTTP_OK);
    }

    public function updateTaskById (TaskRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $task = Task::find($id);

            if (!$task) {
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);
            }

            $task->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'dead_line' => $validatedData['dead_line'],
                'project_id' => $validatedData['project_id'],
                'user_id' => $validatedData['assignee']
            ]);

            return ResponseHelper::success(Constants::SUCCESS_UPDATED, $task, Response::HTTP_OK);
        } catch (Exception $e) {
            \Log::error('Task update failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }


    public function updateTaskStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:1,2,3'
        ]);

        try {
            $task = Task::find($id);

            if (!$task) {
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);
            }

            $task->update([
                'status' => $request->status
            ]);

            return ResponseHelper::success(Constants::SUCCESS_UPDATED, $task, Response::HTTP_OK);
        } catch (Exception $e) {
            \Log::error('Task status update failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }


    public function deleteTaskById ($id)
    {
        try {

            $task = Task::where('id', $id)->first();
            if(!$task){
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);   

            }
            $task->delete();

            return ResponseHelper::success(Constants::SUCCESS_UPDATED, [], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project creation failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());     
        }
    }
}
