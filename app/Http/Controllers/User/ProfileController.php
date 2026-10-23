<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class ProfileController extends Controller
{
    public function edit() { return view('user.profile.edit', ['user' => Auth::user()]); }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'phone'     => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
            'city'      => 'nullable|string|max:100',
            'country'   => 'nullable|string|max:100',
            'address'   => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'avatar'    => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre y apellidos son obligatorios.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'   => 'Ingresa un formato de correo electrónico válido.',
            'email.unique'  => 'Este correo electrónico ya está registrado por otro usuario.',
            'phone.regex'   => 'El número de celular debe tener exactamente 10 dígitos numéricos (ni más ni menos).',
            'avatar.image'  => 'El archivo debe ser una imagen válida.',
            'avatar.mimes'  => 'Solo se permiten imágenes en formato JPG, PNG o WebP.',
            'avatar.max'    => 'La imagen no debe pesar más de 2 MB.',
        ]);

        $data = [
            'name'      => strip_tags(trim($validated['name'])),
            'phone'     => isset($validated['phone']) ? strip_tags(trim($validated['phone'])) : null,
            'city'      => isset($validated['city']) ? strip_tags(trim($validated['city'])) : null,
            'country'   => isset($validated['country']) ? strip_tags(trim($validated['country'])) : null,
            'address'   => isset($validated['address']) ? strip_tags(trim($validated['address'])) : null,
            'birthdate' => $validated['birthdate'] ?? null,
        ];

        // Email update handling
        if ($user->email !== $validated['email']) {
            $data['email'] = strtolower(trim($validated['email']));
        }

        // Avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        // Avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cambios guardados',
                'user'    => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'first_name' => explode(' ', $user->name)[0],
                    'email'      => $user->email,
                    'phone'      => $user->phone ?? '',
                    'city'       => $user->city ?? '',
                    'country'    => $user->country ?? '',
                    'address'    => $user->address ?? '',
                    'birthdate'  => $user->birthdate ? $user->birthdate->format('Y-m-d') : '',
                    'avatar'     => $user->avatar_url,
                    'initial'    => strtoupper(substr($user->name, 0, 1)),
                ]
            ]);
        }

        return back()->with('success', 'Cambios guardados');
    }
}
