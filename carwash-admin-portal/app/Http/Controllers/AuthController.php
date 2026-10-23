<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'branches' => 'nullable|array',
            'branches.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => trim($request->first_name.' '.$request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $branches = $request->branches;
        if (empty($branches)) {
            $branches = ['Main branch'];
        }

        foreach ($branches as $index => $branchName) {
            $codeNum = $index + 1;
            $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $branchName);
            $prefix = strtoupper(substr($cleanName, 0, 4));
            if (empty($prefix)) {
                $prefix = 'BRCH';
            }
            $branchCode = $prefix.'-0'.$codeNum;

            Branch::create([
                'user_id' => $user->id,
                'name' => trim($branchName),
                'code' => $branchCode,
                'location' => 'Kerala',
                'city' => trim($branchName),
                'state' => 'Kerala',
                'phone' => '',
                'gst_number' => '',
            ]);
        }

        // Seed default services for new user
        $defaultServices = [
            ['name' => 'Body wash', 'category' => 'Exterior', 'desc' => 'Exterior body wash with pressure cleaning.', 'price' => 150, 'duration' => 20, 'icon' => 'ti-wash', 'gst_rate' => 5],
            ['name' => 'Foam wash', 'category' => 'Exterior', 'desc' => 'Full foam bath with wax shine finish.', 'price' => 200, 'duration' => 30, 'icon' => 'ti-droplet', 'gst_rate' => 5],
            ['name' => 'Interior cleaning', 'category' => 'Interior', 'desc' => 'Cabin vacuuming, dashboard polish and seat wipe.', 'price' => 300, 'duration' => 40, 'icon' => 'ti-wind', 'gst_rate' => 18],
            ['name' => 'Full wash + Interior', 'category' => 'Bundle', 'desc' => 'Complete exterior foam wash with interior cleaning.', 'price' => 400, 'duration' => 50, 'icon' => 'ti-sparkles', 'gst_rate' => 18],
            ['name' => 'Engine wash', 'category' => 'Detailing', 'desc' => 'Engine bay degreasing and cleaning.', 'price' => 250, 'duration' => 25, 'icon' => 'ti-engine', 'gst_rate' => 18],
            ['name' => 'AC vent cleaning', 'category' => 'Detailing', 'desc' => 'AC vent and cabin air filter cleaning.', 'price' => 200, 'duration' => 20, 'icon' => 'ti-air-conditioning', 'gst_rate' => 18],
            ['name' => 'Headlight restoration', 'category' => 'Detailing', 'desc' => 'UV-damaged headlight lens polishing and restoration.', 'price' => 800, 'duration' => 45, 'icon' => 'ti-bulb', 'gst_rate' => 18],
            ['name' => 'Ceramic coating', 'category' => 'Premium', 'desc' => 'Durable high-gloss ceramic paint protection.', 'price' => 3500, 'duration' => 120, 'icon' => 'ti-shield', 'gst_rate' => 18],
            ['name' => 'Graphene coating', 'category' => 'Premium', 'desc' => 'Premium multi-year graphene protective shield.', 'price' => 5000, 'duration' => 180, 'icon' => 'ti-atom-2', 'gst_rate' => 18],
        ];
        foreach ($defaultServices as $s) {
            Service::create(array_merge($s, ['user_id' => $user->id]));
        }

        Auth::login($user, true);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful.',
            'user' => $user,
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember', false))) {
            $request->session()->regenerate();
            $user = Auth::user()->load('assignedBranch');

            return response()->json([
                'status' => 'success',
                'message' => 'Logged in successfully.',
                'user' => $user,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'status' => 'success',
                'user' => Auth::user(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
