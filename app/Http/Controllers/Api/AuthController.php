<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name, 
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Registrasi berhasil',
            'data'    => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil',
            'data'    => $user
        ], 200);
    }

    public function getUserDetail($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Data user ditemukan',
            'data'    => $user
        ], 200);
    }

    public function updateUser(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $user_id,
            'password' => 'sometimes|nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Update gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password') && $request->password != null) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil diperbarui',
            'data'    => $user
        ], 200);
    }

    public function deleteUser($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}