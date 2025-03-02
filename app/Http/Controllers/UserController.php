<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('user');
    }

    public function getData()
    {
        $users = DB::table('users');

        return DataTables::of($users)
            ->addColumn('action', function ($user) {
                return '<button type="button" class="btn btn-primary edit-btn" data-id="' . $user->id . '">Edit</button>';
            })
            ->editColumn('status', function ($user) {
                // Ensure status is treated as an integer (0 or 1)
                $iconClass = $user->status === 0 ? 'fa-check' : 'fa-times'; // Check for 0 for active, 1 for inactive
                $buttonClass = $user->status === 0 ? 'btn-success' : 'btn-danger'; // Button class based on status
                $buttonText = $user->status === 0 ? 'Active' : 'Inactive'; // Text based on status

                return '<button type="button" class="btn ' . $buttonClass . ' toggle-status" data-id="' . $user->id . '"><i class="fa ' . $iconClass . '"></i> ' . $buttonText . '</button>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }


    public function store(Request $request)
{
    // Validate the incoming data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:8|confirmed', // 'confirmed' will match 'password_confirmation'
        'role' => 'required|in:admin,warga',
    ]);

    // Hash the password
    $hashedPassword = Hash::make($validated['password']);

    // Insert the new user into the database
    $userId = DB::table('users')->insertGetId([ // Using insertGetId to get the inserted user's ID
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => $hashedPassword,
        'role' => $validated['role'], // Use the validated 'role' field
        'status' => true,  // Default to active
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Check if the insert was successful and return a response
    if ($userId) {
        return response()->json([
            'message' => 'User created successfully.',
            'user' => DB::table('users')->find($userId) // Optional: Return the created user as JSON
        ]);
    } else {
        return response()->json(['error' => 'Failed to create user.'], 500); // Return a 500 error if insert fails
    }
}


    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,warga',
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $affectedRows = DB::table('users')
            ->where('id', $id)
            ->update($dataToUpdate);

        return response()->json(['success' => $affectedRows > 0]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('users')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        return response()->json(['success' => true]);
    }

    public function toggleStatus(Request $request)
    {
        // Toggle the 'status' for the user
        $currentStatus = DB::table('users')
            ->where('id', $request->id)
            ->value('status');

        $newStatus = !$currentStatus; // Flip the current status (0 becomes 1, 1 becomes 0)
        DB::table('users')
            ->where('id', $request->id)
            ->update(['status' => $newStatus]);

        return response()->json(['status' => $newStatus]);
    }
}
