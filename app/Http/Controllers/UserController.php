<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Spatie\Permission\Models\Role;
use Exception;
use DB;

class UserController extends Controller
{
    public function userList(Request $request){
        $roles = Role::pluck('name', 'id')->toArray(); 
        if($request->ajax()){
            $search =$request->search['value'];

            $query = User::select('users.*', 'roles.name as role_name', 'roles.id as role_id')
                        ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->when($search, function ($query) use ($search) {
                            $query->where('users.name', 'like', "%$search%")
                            ->orWhere('roles.name', 'like', "%$search%");
                        })
                        ->where('users.name', '!=', 'Admin User');

            if ($request->has('order.0.column') && $request->has('order.0.dir')) {
                $orderBy = $request->columns[$request->input('order.0.column')]['data'];
                $orderDirection = $request->input('order.0.dir');
                $query->orderBy($orderBy, $orderDirection);
            }else{
                $query->orderBy('users.created_at', 'desc');
            }

            $totalRecords = $query->count();
            $start = $request->input('start');
            $length = $request->input('length');
            $query->skip($start)->take($length);
            $data = $query->get();

            $data->transform(function ($row) {
                $buttons = '';
               
                if (auth()->user()->can('user_read')) {
                    $viewButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-role="' . $row->role_id . '" data-name="' . $row->name . '" id="viewUser" class="text-blue-500 hover:text-blue-700 mx-1" title="View User">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>';
                    $buttons .= $viewButton;
                }
                
                if (auth()->user()->can('user_delete')) {
                    $deleteButton = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" id="deleteUser" class="text-red-500 hover:text-red-700 mx-1" title="Delete User">
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
        return view('users', compact('roles'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);
        try {
            $user = User::find($id);
            if(!$user){
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);   
            }

            // Revoke old role(s)
            $user->roles()->detach();
            // Assign new role
            DB::table('model_has_roles')->insert([
                'role_id' => $request->role_id,
                'model_id' => $id,
                'model_type' => 'App\Models\User',
            ]);

            return ResponseHelper::success(Constants::SUCCESS_UPDATED, $user, Response::HTTP_OK);

        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function deleteUserById($id)
    {
        try {
            $user = User::where('id', $id)->first();
            if(!$user){
                return ResponseHelper::error(Constants::ERROR_INVALID_REQUEST, Response::HTTP_BAD_REQUEST);   

            }
            // Optionally revoke roles before deleting (if needed)
            $user->roles()->detach();

            $user->delete();

            return ResponseHelper::success(Constants::SUCCESS_UPDATED, [], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return ResponseHelper::error(Constants::ERROR_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());     
        }
    }

}
