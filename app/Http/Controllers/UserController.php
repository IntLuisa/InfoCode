<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserService $userService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $build = User::query()->where('id', '!=', 1);

        $filter = (new UserFilter($request, $build))
            ->getBuilder()->appends($request->query())
            ->toArray();

        if ($request->has('json')) {
            return $filter['data'];
        } else {
            return Inertia::render('User/Index', [
                'users' => $filter['data'],
                'pagination' => $filter
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        return $this->userService->create($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update', User::class);
        return $this->userService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', User::class);
        return $this->userService->delete($id);
    }

    public function restore(string $id)
    {
        $this->authorize('restore', User::class);
        $user = User::withTrashed()->findOrFail($id);
        return $user->restore();
    }
}
