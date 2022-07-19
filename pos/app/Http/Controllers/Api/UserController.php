<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Carbon\Carbon;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserController extends Controller
{
    /**
     * Get all user information
     *
     * @param Request $request
     * @return JsonObject user information
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['permissions'])
            ->get();
        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        $query = User::withTrashed()->where('id', $id);
        $user = QueryBuilder::for($query)
            ->allowedIncludes(['permissions'])
            ->firstOrFail();
        return new UserResource($user);
    }

    /**
     * Add new User
     *
     * @param Request $request
     * @return JsonObject user information
     */
    public function store(StoreRequest $request)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        $this->handlePasswordInput($request);

        $user = User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'position' => $request->position,
            'date_join' => $request->date_join,
            'date_left' => $request->date_left,
            'username' => $request->username,
            'starting_position' => $request->starting_position,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'address' => $request->address,
            'phone' => $request->phone,
            'remark' => $request->remark,
        ]);

        // if image is provided, also upload image
        if ($request->image && $request->input('image') != "") {
            $user->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                ->toMediaCollection(User::$mediaCollectionPath);
        }

        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');

        $permissions = [];
        if ($request->permissions) {
            for ($index = 0; $index < count($request->permissions); $index++) {
                $user->givePermissionTo($request->input('permissions.' . $index . '.id'));
            }
        }

        return new UserResource($user);
    }

    /**
     * Update User
     *
     * @param Request $request
     * @return JsonObject user information
     */
    public function update(UpdateRequest $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        $this->handlePasswordInput($request);

        $user->update($request->only([
            'name' => $request->name,
            'password' => $request->password,
            'position' => $request->position,
            'date_join' => $request->date_join,
            'date_left' => $request->date_left,
            'username' => $request->username,
            'starting_position' => $request->starting_position,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'address' => $request->address,
            'phone' => $request->phone,
            'remark' => $request->remark,
        ]));

        // if image is provided, also upload image
        if ($request->image) {
            $user->clearMediaCollection(User::$mediaCollectionPath);

            if ($request->input('image') != "") {
                $user->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                    ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                    ->toMediaCollection(User::$mediaCollectionPath);
            }
        }

        if ($request->input('password') && $request->input('password') != '') {
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // otherwise, changes won't have effect
        \Cache::forget('spatie.permission.cache');

        $permissions = [];
        if ($request->permissions) {
            for ($index = 0; $index < count($request->permissions); $index++) {
                $user->givePermissionTo($request->input('permissions.' . $index . '.id'));
                array_push($permissions, $request->input('permissions.' . $index . '.id'));
            }
        }
        $user->revokePermissionTo($user->permissions()->whereNotIn('id', $permissions)->get());

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->hasPermissionTo('Manage user')) {
            return response()->json(['message' => 'Tidak ada akses'], 403);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) $user->restore();

        return new UserResource($user);
    }

    /**
     * Get current authenticated user information
     *
     * @param Request $request
     * @return JsonObject user information
     */
    public function getUserInfo(Request $request)
    {
        return response()->json(['data' => new UserResource($request->user())], 200);
    }

    /**
     * get current authenticated user role
     *
     * @return Array roles
     */
    public function getUserRole()
    {
        return response()->json(['data' => auth()->user()->getRoleNames()], 200);
    }

    /**
     * get current authenticated user permission
     *
     * @return Array permissions
     */
    public function getUserPermission(Request $request)
    {
        return response()->json(['data' => auth()->user()->getAllPermissions()], 200);
    }

    /**
     * Handle password input fields.
     *
     * @param Request $request
     */
    protected function handlePasswordInput(Request $request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }
    }
}
