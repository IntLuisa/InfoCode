<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class UserService
{
    public function create(Request $request): User
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'job_position' => 'required|string|max:255',
            'role' => 'required|string|max:30',
            'photo' => 'nullable|image|max:1024',
        ]);

        $user = User::create([
            ...$data,
            'password' => bcrypt(md5(time())),
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $path = $photo->store('user_photos', 'public');
            $user->photo = $path;
            $user->save();
        }

        return $user;
    }

    public function update(Request $request, string $id): User
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'job_position' => 'required|string|max:255',
            'role' => 'required|string|max:30',
            'photo' => 'nullable|image|max:1024',
        ]);

        $user->update($data);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $path = $photo->store('user_photos', 'public');
            $user->photo = $path;
            $user->save();
        }

        return $user;
    }

    public function delete(string $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
