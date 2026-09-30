<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'role']);
        $preTestId = Test::query()->where('kind', Test::KIND_PRE_TEST)->value('id');

        $users = User::query()
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('nim', 'like', "%{$search}%")))
            ->when($filters['role'] ?? null, fn ($q, $role) => $q->where('role', $role))
            ->with(['testAttempts' => fn ($q) => $q
                ->where('test_id', $preTestId)
                ->where('status', 'completed')
                ->select(['id', 'user_id', 'score'])])
            ->orderByRaw("case when role = 'admin' then 0 else 1 end")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'email' => $u->email,
                'role' => $u->role,
                'nim' => $u->nim,
                'semester' => $u->semester,
                'program_studi' => $u->program_studi,
                'pre_test_score' => $u->testAttempts->first()?->score !== null ? (float) $u->testAttempts->first()->score : null,
                'is_self' => $u->is($request->user()),
                'created_at' => $u->created_at?->locale('id')->isoFormat('D MMM YYYY'),
            ]);

        return Inertia::render('admin/pengguna/Index', [
            'users' => $users,
            'filters' => $filters,
            'counts' => [
                'all' => User::query()->count(),
                'mahasiswa' => User::query()->where('role', 'mahasiswa')->count(),
                'admin' => User::query()->where('role', 'admin')->count(),
            ],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::query()->create($request->safe()->except('password_confirmation'));
        $user->forceFill(['email_verified_at' => now()])->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => "Pengguna {$user->name} ditambahkan."]);

        return back();
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Data {$user->name} diperbarui."]);

        return back();
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.']);

            return back();
        }

        if ($user->isAdmin() && User::query()->where('role', 'admin')->count() <= 1) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Minimal harus ada satu admin.']);

            return back();
        }

        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => "Pengguna {$user->name} dihapus."]);

        return back();
    }
}
