<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'all');
        $search = trim((string) $request->get('q', ''));

        $query = User::query();

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $counts = [
            'all'      => User::count(),
            'user'     => User::where('role', 'user')->count(),
            'business' => User::where('role', 'business')->count(),
            'admin'    => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'search', 'counts'));
    }

    public function create() { return view('admin.users.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role'     => 'required|in:user,business,admin',
        ]);
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'email_verified_at' => now(),
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Usuario registrado correctamente en el sistema.');
    }

    public function edit(User $user)
    {
        return redirect()->route('admin.users.index')
            ->with('warning', 'La edición de datos privados de usuarios está deshabilitada en el panel de administración. Solo se permite verificar cuentas o eliminarlas.');
    }

    public function update(Request $request, User $user)
    {
        return redirect()->route('admin.users.index')
            ->with('warning', 'La edición de datos privados de usuarios está deshabilitada en el panel de administración.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', "Usuario '{$user->name}' eliminado correctamente.");
    }

    public function toggleActive(User $user)
    {
        $user->update(['active' => !$user->active]);
        $statusText = $user->active ? 'activada' : 'desactivada';
        return back()->with('success', "La cuenta de '{$user->name}' ha sido {$statusText}.");
    }

    public function verifyAccount(User $user)
    {
        $user->update(['email_verified_at' => now()]);
        return back()->with('success', "La cuenta de '{$user->name}' ({$user->email}) ha sido verificada correctamente.");
    }
}
