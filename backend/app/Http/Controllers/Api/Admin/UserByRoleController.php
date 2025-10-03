<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTraits;
use App\Models\UserByRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserByRoleController extends Controller
{
    use ApiResponseTraits;

    /**
     * Display a listing of user by roles
     */
    public function index()
    {
        try {
            $userByRoles = UserByRole::with('upt')
                ->select(['id', 'phone_number', 'upt_code', 'role', 'status', 'bio', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($userByRoles, 'Data user by roles berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data user by roles');
        }
    }

    /**
     * Store a newly created user by role
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone_number' => 'nullable|string|max:30',
                'upt_code' => 'nullable|string|max:100|exists:mas_upts,upts_code|unique:user_by_role,upt_code',
                'role' => 'required|in:admin,user,moderator',
                'status' => 'required|in:active,inactive,pending',
                'bio' => 'nullable|string',
            ]);

            $userByRole = UserByRole::create($validated);

            return $this->successResponse($userByRole->load('upt'), 'User by role berhasil dibuat', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal membuat user by role');
        }
    }

    /**
     * Display the specified user by role
     */
    public function show($id)
    {
        try {
            $userByRole = UserByRole::with('upt')
                ->findOrFail($id);

            return $this->successResponse($userByRole, 'Data user by role berhasil diambil');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('User by role tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data user by role');
        }
    }

    /**
     * Update the specified user by role
     */
    public function update(Request $request, $id)
    {
        try {
            $userByRole = UserByRole::findOrFail($id);

            $validated = $request->validate([
                'phone_number' => 'nullable|string|max:30',
                'upt_code' => [
                    'nullable',
                    'string',
                    'max:100',
                    'exists:mas_upts,upts_code',
                    Rule::unique('user_by_role', 'upt_code')->ignore($userByRole->id)
                ],
                'role' => 'required|in:admin,user,moderator',
                'status' => 'required|in:active,inactive,pending',
                'bio' => 'nullable|string',
            ]);

            $userByRole->update($validated);

            return $this->successResponse($userByRole->load('upt'), 'User by role berhasil diupdate');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('User by role tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengupdate user by role');
        }
    }

    /**
     * Remove the specified user by role
     */
    public function destroy($id)
    {
        try {
            $userByRole = UserByRole::findOrFail($id);
            $userByRole->delete();

            return $this->successResponse(null, 'User by role berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('User by role tidak ditemukan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus user by role');
        }
    }

    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        try {
            $userByRoles = UserByRole::with('upt')
                ->where('role', $role)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($userByRoles, 'Data users berdasarkan role berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data users berdasarkan role');
        }
    }

    /**
     * Get users by status
     */
    public function getByStatus($status)
    {
        try {
            $userByRoles = UserByRole::with('upt')
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($userByRoles, 'Data users berdasarkan status berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data users berdasarkan status');
        }
    }

    /**
     * Get users by UPT
     */
    public function getByUpt($uptCode)
    {
        try {
            $userByRoles = UserByRole::with('upt')
                ->where('upt_code', $uptCode)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($userByRoles, 'Data users berdasarkan UPT berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data users berdasarkan UPT');
        }
    }
}