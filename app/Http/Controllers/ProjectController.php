<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProjectRequest;
use DB;
use App\Helpers\ResponseHelper;
use App\Utils\Constants;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:project_read'])->only(['projectList', 'getProjectById']);
        $this->middleware(['permission:project_update'])->only(['updateProjectById']);
        $this->middleware(['permission:project_create'])->only(['store']);
        $this->middleware(['permission:project_delete'])->only(['deleteProjectById']);
    }

    public function projectList(Request $request)
    {
        if($request->ajax()){
            $search =$request->search['value'];

            $query = Project::query()
                        ->when($search, function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
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
                               
                if (auth()->user()->can('project_update')) {
                    $editButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" id="editProject" class="text-yellow-500 hover:text-yellow-700 mx-1">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>';
                    $buttons .= $editButton;
                }
                
                if (auth()->user()->can('project_delete')) {
                    $deleteButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" id="deleteProject" class="text-red-500 hover:text-red-700 mx-1">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>';
                    $buttons .= $deleteButton;
                }

                $row->action = $buttons;
                return $row;
            });

            return response()->json([
                'success' => true,
                'message' => 'Projects retrieved successfully.',
                'data' => $data,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
            ], 200);
        }
        $teamMembers = User::all();
        return view('projects', compact('teamMembers'));
    }

    public function store(ProjectRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $project = Project::create($request->validated());
            $project->teamMembers()->sync($request->team_members);

            DB::commit();
            return ResponseHelper::success(Constants::SUCCESS_CREATED, $project, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project creation failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());   
        }
    }


    public function getProjectById($id)
    {
        $project = Project::select('id', 'name', 'start_date', 'end_date', 'description')
                    ->with('teamMembers:id')
                    ->find($id);

        return ResponseHelper::success(Constants::SUCCESS_DATA_FETCHED, $project, Response::HTTP_OK);
    }

    public function updateProjectById (ProjectRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $project = Project::where('id', $id)->first();
            if(!$project){
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);   
            }
            $project->update($request->validated());
            $project->teamMembers()->sync($request->team_members);

            DB::commit();
            return ResponseHelper::success(Constants::SUCCESS_UPDATED, $project, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project creation failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());   
        }
    }


    public function deleteProjectById($id)
    {
        try {

            $project = Project::where('id', $id)->first();
            if(!$project){
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);   

            }
            $project->delete();
            return ResponseHelper::success(Constants::SUCCESS_DELETED, [], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Project creation failed: ' . $e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());   
        }
    }

    public function getTeamMembers($id)
    {
        try {
            $project = Project::find($id);
            if (!$project) {
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);   
            }
            $teamMembers = $project->teamMembers;
            return ResponseHelper::success(Constants::SUCCESS_DATA_FETCHED, $teamMembers, Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Project creation failed: ' . $e->getMessage());           
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());   
        }
    }
}
