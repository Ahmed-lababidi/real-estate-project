<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        $admin = Admin::query()->where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return $this->errorResponse('Invalid credentials', 422);
        }

        if (! $admin->is_active) {
            return $this->errorResponse('This admin account is inactive', 403);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        $admin->update([
            'last_login_at' => now(),
        ]);

        return $this->successResponse([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
            ],
            'token' => $token,
        ], 'Login successful');
    }

    public function logout()
    {
        auth()->user()?->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    public function me()
    {
        $admin = auth()->user();

        return $this->successResponse([
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
        ], 'Admin profile fetched successfully');
    }
}
