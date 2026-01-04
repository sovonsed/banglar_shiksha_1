<?php
// app/Http\Controllers\Admin\UserController.php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\DistrictMaster;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function __construct()
    {
        // Apply authorization middleware
        $this->middleware('permission:view users')->only(['index', 'show']);
        $this->middleware('permission:create users')->only(['create', 'store']);
        $this->middleware('permission:edit users')->only(['edit', 'update']);
        $this->middleware('permission:delete users')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles' => function ($query) {
            $query->select('id', 'name');
        }])
            ->withCount(['permissions as direct_permissions_count'])
            ->latest();

        // Advanced Search Functionality
        if ($request->hasAny(['search', 'status', 'role', 'department', 'date_from', 'date_to'])) {

            // Global search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status') && $request->status != 'all') {
                $query->where('status', $request->status == 'active');
            }

            // Role filter
            if ($request->filled('role') && $request->role != 'all') {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

            // Department filter
            if ($request->filled('department') && $request->department != 'all') {
                $query->where('department', $request->department);
            }

            // Date range filter (created_at)
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Online status filter
            if ($request->filled('online_status')) {
                if ($request->online_status == 'online') {
                    $query->where('last_login_at', '>', now()->subMinutes(5));
                } elseif ($request->online_status == 'offline') {
                    $query->where(function ($q) {
                        $q->whereNull('last_login_at')
                            ->orWhere('last_login_at', '<=', now()->subMinutes(5));
                    });
                }
            }
        }

        // Get all unique departments for filter
        $departments = User::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->values();

        // Get stats (unfiltered)
        $activeUsersCount = User::where('status', true)->count();
        $inactiveUsersCount = User::where('status', false)->count();
        $onlineUsersCount = User::where('last_login_at', '>', now()->subMinutes(5))->count();

        // Get all roles for filter dropdown
        $roles = Role::pluck('name', 'name');

        // Paginate results - ensure proper pagination
        $users = $query->paginate(12)->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'activeUsersCount',
            'inactiveUsersCount',
            'onlineUsersCount',
            'roles',
            'departments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'name')->all();

        $districts = DistrictMaster::orderBy('name')->get()
            ->map(function ($district) {
                $district->encrypted_id = Crypt::encrypt($district->id);
                return $district;
            });

        return view('admin.users.create', compact('roles', 'districts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'dise_code' => 'nullable|string|min:2|max:11|regex:/^[0-9]+$/',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'same:confirm-password'
            ],
            'role' => 'required|string|exists:roles,name'
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'dise_code.min' => 'DISE code must be at least 2 digits.',
            'dise_code.max' => 'DISE code cannot exceed 11 digits.',
            'dise_code.regex' => 'DISE code must contain only numbers (0-9).',
            'role.required' => 'Please select a role for the user.'
        ]);

        try {
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $input['status'] = true;
            $input['sso_id'] = Auth::user()->sso_id;

            DB::transaction(function () use ($input, $request) {
                $user = User::create($input);
                $user->assignRole($request->input('role'));

                // Sync user with central SSO app
                $this->syncUserToCentralApp($user, $request['password']);
            });

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Sync user to central SSO application
     */
    private function syncUserToCentralApp(User $user, string $plainPassword): bool
    {
        try {
            $timestamp = now()->timestamp;
            $appId = config('sso.app_id');

            // Prepare payload for central app
            $payload = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department' => $user->department,
                'designation' => $user->designation,
                'dise_code' => $user->dise_code, // Include dise_code
                'password' => $plainPassword, // Send plain password for central app to hash
                'status' => $user->status,
                'app_id' => $appId,
                'timestamp' => $timestamp,
                'created_by' => Auth::user()->sso_id, // Reference to creator in central app
            ];

            // Generate signature for verification
            $signatureData = $timestamp . $user->email . $appId . $user->dise_code;
            $payload['signature'] = hash_hmac('sha256', $signatureData, config('sso.secret_key'));

            // Central app user creation endpoint
            $centralUserCreateUrl = config('sso.auth_server') . '/api/sso/users/create';

            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->post($centralUserCreateUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                // Update local user with central app user ID
                if (isset($responseData['data']['sso_id'])) {
                    $user->update([
                        'sso_id' => $responseData['data']['sso_id'],
                        'central_sync_at' => now(),
                    ]);
                }

                Log::info('User synced to central app successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'dise_code' => $user->dise_code,
                    'central_user_id' => $responseData['data']['sso_id'] ?? null
                ]);

                return true;
            } else {
                Log::error('Failed to sync user to central app', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                // You might want to queue this for retry or notify admin
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception syncing user to central app', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::findOrFail($id);
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Get roles with proper ordering and exclude Super Admin if needed
        $roles = Role::orderBy('name')->get();

        // If user is not Super Admin, filter out Super Admin role
        if (!auth()->user()->hasRole('Super Admin')) {
            $roles = $roles->filter(function ($role) {
                return $role->name !== 'Super Admin';
            });
        }

        $userRole = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Cannot modify Super Admin user.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'password' => 'nullable|same:confirm-password',
            'role' => 'required',
            'status' => 'boolean'
        ]);

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $input['status'] = $request->has('status') ? true : false;

        DB::transaction(function () use ($id, $input, $request) {
            $user = User::find($id);
            $oldData = $user->toArray(); // Store old data for sync

            $user->update($input);
            $user->syncRoles($request->input('role'));

            // Sync update to central app if user has sso_id
            if ($user->sso_id && !empty($input['password'])) {
                $this->syncUserUpdateToCentralApp($user, $input['password'], $oldData);
            } elseif ($user->sso_id) {
                $this->syncUserUpdateToCentralApp($user, null, $oldData);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }


    /**
     * Sync user update to central SSO application
     */
    private function syncUserUpdateToCentralApp(User $user, ?string $plainPassword = null, array $oldData = []): bool
    {
        try {
            $timestamp = now()->timestamp;
            $appId = config('sso.app_id');

            $payload = [
                'sso_id' => $user->sso_id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department' => $user->department,
                'designation' => $user->designation,
                'dise_code' => $user->dise_code,
                'status' => $user->status,
                'app_id' => $appId,
                'timestamp' => $timestamp,
            ];

            // Include password only if provided
            if ($plainPassword) {
                $payload['password'] = $plainPassword;
            }

            // Generate signature
            $signatureData = $timestamp . $user->sso_id . $appId . $user->dise_code;
            $payload['signature'] = hash_hmac('sha256', $signatureData, config('sso.secret_key'));

            $centralUserUpdateUrl = config('sso.auth_server') . '/api/sso/users/update';

            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->put($centralUserUpdateUrl, $payload);

            if ($response->successful()) {
                $user->update(['central_sync_at' => now()]);

                Log::info('User update synced to central app successfully', [
                    'user_id' => $user->id,
                    'sso_id' => $user->sso_id,
                    'dise_code' => $user->dise_code
                ]);

                return true;
            } else {
                Log::error('Failed to sync user update to central app', [
                    'user_id' => $user->id,
                    'sso_id' => $user->sso_id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception syncing user update to central app', [
                'user_id' => $user->id,
                'sso_id' => $user->sso_id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($id == auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Super Admin user cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,impersonate',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->users)
            ->where('id', '!=', auth()->id())
            ->get();

        DB::transaction(function () use ($request, $users) {
            foreach ($users as $user) {
                if ($user->hasRole('Super Admin')) {
                    continue;
                }

                switch ($request->action) {
                    case 'activate':
                        $user->update(['status' => true]);
                        break;
                    case 'deactivate':
                        $user->update(['status' => false]);
                        break;
                    case 'delete':
                        $user->delete();
                        break;
                }
            }
        });

        // Handle impersonate separately as it can only target one user
        if ($request->action === 'impersonate' && count($request->users) === 1) {
            $user = User::find($request->users[0]);
            if ($user && !$user->hasRole('Super Admin') && $user->id != auth()->id()) {
                return $this->impersonate($user);
            }
        }

        return redirect()->back()->with('success', 'Bulk action completed successfully.');
    }

    public function impersonate(User $user)
    {
        if (!auth()->user()->can('impersonate users')) {
            abort(403);
        }

        if ($user->id === auth()->id() || $user->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Cannot impersonate this user.');
        }

        $originalUser = auth()->user();
        $originalUser->update([
            'impersonator_id' => null,
        ]);

        $user->update([
            'impersonator_id' => $originalUser->id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Now impersonating {$user->name}");
    }

    public function stopImpersonate()
    {
        $currentUser = auth()->user();

        if (!$currentUser->impersonator_id) {
            return redirect()->route('dashboard');
        }

        $originalUser = User::find($currentUser->impersonator_id);

        if ($originalUser) {
            $currentUser->update(['impersonator_id' => null]);
            Auth::login($originalUser);
            return redirect()->route('admin.users.index')->with('success', 'Stopped impersonating user.');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Clear all search filters
     */
    public function clearFilters(): RedirectResponse
    {
        return redirect()->route('admin.users.index');
    }
}
