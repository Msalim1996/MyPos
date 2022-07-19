<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RoleHasPermission;
use App\Models\Permission;
use App\Models\Role;
use App\Http\Resources\RoleHasPermissionResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;

/**
 * @group Spatie Permission API
 */
class RoleHasPermissionController extends Controller
{
    public function getRoleHasPermissionById($roleId)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }
        
        $role_has_permission = RoleHasPermission::where('role_id', '=', $roleId)->get();

        $arr = [];

        foreach ($role_has_permission as $rhp) {
            if (!isset($arr[$rhp->role_id])) {
                $arr[$rhp->role_id] = [];
            }
            array_push($arr[$rhp->role_id], PermissionResource::make(Permission::find($rhp->permission_id)));
        }

        $result = [];

        foreach ($arr as $key => $permission) {
            $result = [
                'role_id' => RoleResource::make(Role::find($key)),
                'permission_id' => $permission,
            ];
        }

        return $result;
    }

    public function index()
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }
        
        return RoleHasPermissionResource::collection(RoleHasPermission::all());
    }
    
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($roleId)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }
        
        $role_has_permission = RoleHasPermission::where('role_id', '=', $roleId)->get();

        $arr = [];

        foreach ($role_has_permission as $rhp) {
            if (!isset($arr[$rhp->role_id])) {
                $arr[$rhp->role_id] = [];
            }
            array_push($arr[$rhp->role_id], PermissionResource::make(Permission::find($rhp->permission_id)));
        }

        $result = [];

        foreach ($arr as $key => $permission) {
            array_push($result, [
                'role_id' => RoleResource::make(Role::find($key)),
                'permission_id' => $permission,
            ]);
        }

        $roleHasPermission = RoleHasPermission::select('role_id')->distinct()->get();
        $roleHasPermission = $roleHasPermission->map(function ($el) {
            return $el->role_id;
        });

        $role = Role::select('id')->where('id', '=', $roleId)->get();
        $role = $role->map(function ($el) {
            return $el->id;
        });

        $diff = array_diff($role->toArray(), $roleHasPermission->toArray());

        foreach ($diff as $key => $value) {
            array_push($result, [
                'role_id' => RoleResource::make(Role::find($value)),
                'permission_id' => []
            ]);
        }

        if (count($result) == 0) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return ['data' => $result];
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');
            
        $role_has_permission = RoleHasPermission::create([
            'permission_id' => $request->permission_id,
            'role_id' => $request->role_id,
        ]);
    
        return $this->show($request->role_id);
    }

    public function multiStore(Request $request, $roleId)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }
        
        $permission_id = $request->data;

        $arr = [];

        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');

        foreach ($permission_id as $value) {
            $role_has_permission = RoleHasPermission::create([
                'permission_id' => $value,
                'role_id' => $roleId,
            ]);
                
            array_push($arr, PermissionResource::make(Permission::find($value)));
        }

        return [
            'role_id' => RoleResource::make(Role::find($roleId)),
            'permission_id' => $arr
        ];
    }

    public function multiUpdate(Request $request, $roleId)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        
        $permission_id = $request->data;

        $arr = [];

        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');

        $role_has_permission = RoleHasPermission::where('role_id', '=', $roleId)->get();
            
        foreach ($role_has_permission as $val) {
            $role_has_permission = RoleHasPermission::where('role_id', '=', $roleId)
                ->where('permission_id', '=', $val->permission_id)
                ->delete();
        }
        foreach ($permission_id as $value) {
            $role_has_permission = RoleHasPermission::create([
                'permission_id' => $value,
                'role_id' => $roleId,
            ]);
                
            array_push($arr, PermissionResource::make(Permission::find($value)));
        }

        return [
            'role_id' => RoleResource::make(Role::find($roleId)),
            'permission_id' => $arr
        ];
    }

    public function multiDestroy(Request $request, $roleId)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }
        
        $permission_id = $request->data;
            
        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');

        foreach ($permission_id as $value) {
            $role_has_permission = RoleHasPermission::where('role_id', '=', $roleId)
                ->where('permission_id', '=', $value)
                ->delete();

            if (!$role_has_permission) {
                return response()->json(null, 404);
            }
        }

        return response()->json(null, 204);
    }

    public function destroy($roleId, $permissionId)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }
        
        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');
            
        $role_has_permission = RoleHasPermission::where('role_id', '=', $roleId)
            ->where('permission_id', '=', $permissionId)
            ->delete();
        if ($role_has_permission) {
            return response()->json(null, 204);
        }
            
        return response()->json(null, 404);
    }
}
