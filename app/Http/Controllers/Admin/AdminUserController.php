<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    use ApiResponse;

    public function index()   { return $this->success(User::paginate(20)); }
    public function show(User $user) { return $this->success($user); }

    public function update(Request $request, User $user)
    {
        $user->update($request->only('name','department','phone','is_active','role'));
        return $this->success($user, 'User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->success(null, 'User deleted');
    }
}