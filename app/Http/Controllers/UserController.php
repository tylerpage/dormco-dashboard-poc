<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can access user management.');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAdmin();
        $users = User::orderBy('name')->paginate(20);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAdmin();
        $schools = School::where('is_active', true)->get();
        return view('users.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,school',
            'assigned_schools' => 'nullable|array',
            'assigned_schools.*' => 'exists:schools,id',
            'permissions' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'assigned_schools' => $request->assigned_schools,
            'permissions' => $request->permissions,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->checkAdmin();
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->checkAdmin();
        $schools = School::where('is_active', true)->get();
        return view('users.edit', compact('user', 'schools'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdmin();
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|in:admin,staff,school',
                'assigned_schools' => 'nullable|array',
                'assigned_schools.*' => 'exists:schools,id',
                'permissions' => 'nullable|array',
            ]);

            $data = $request->only(['name', 'email', 'role', 'assigned_schools', 'permissions']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle empty arrays for assigned_schools and permissions
            if (!$request->has('assigned_schools')) {
                $data['assigned_schools'] = [];
            }
            if (!$request->has('permissions')) {
                $data['permissions'] = [];
            }

            $user->update($data);

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->checkAdmin();
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
