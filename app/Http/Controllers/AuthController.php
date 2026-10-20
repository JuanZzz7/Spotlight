<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:user,business,admin',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Si es admin, permitimos el acceso sin importar el rol seleccionado
            // Para otros roles, validamos que coincida con el formulario
            if ($user->role !== 'admin' && $user->role !== $request->role) {
                Auth::logout();
                return back()->withErrors(['role' => 'El rol seleccionado no corresponde a tu cuenta.']);
            }
            $request->session()->regenerate();
            return match($user->role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'business' => $user->company ? redirect()->route('business.dashboard') : redirect()->route('business.company.edit'),
                default    => redirect()->route('user.dashboard'),
            };
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role'     => 'required|in:user,business',
        ];

        $country = strtoupper((string) $request->input('country', 'CO'));
        if (!in_array($country, ['CO', 'MX'])) {
            $country = 'CO';
        }

        // Reglas condicionales si es Empresario o Comprador
        if ($request->role === 'business') {
            $businessFormRequest = \App\Http\Requests\BusinessRegisterRequest::createFrom($request);
            $businessRules = $businessFormRequest->rules();
            $customMessages = array_merge($businessFormRequest->messages(), [
                'name.regex' => 'El nombre completo solo puede contener caracteres alfabéticos (A-Z, a-z).',
            ]);
            $businessRulesToMerge = array_intersect_key($businessRules, array_flip([
                'country', 'state', 'city', 'postal_code', 'tax_id', 'currency', 'phone_prefix', 'phone',
                'tipo_negocio', 'habeas_data_accepted', 'address',
                'razon_social', 'nit', 'camara_comercio_file', 'rut_file',
                'nombre_comercial', 'cedula_propietario', 'rut_personal_file',
                'nombre_representante', 'email_representante',
                'latitude', 'longitude', 'category', 'description', 'logo'
            ]));
            $rules = array_merge($rules, $businessRulesToMerge);
        } else {
            // Comprador (role: user) - No requiere ningún documento tributario ni requisitos complejos
            $rules['phone'] = ['nullable', 'string', 'max:20'];
            $rules['phone_prefix'] = ['nullable', 'string', 'max:10'];
            $rules['city'] = ['nullable', 'string', 'max:100'];
            $rules['state'] = ['nullable', 'string', 'max:100'];
            $rules['postal_code'] = ['nullable', 'string', 'max:20'];
            $rules['address'] = ['nullable', 'string', 'max:255'];
            $customMessages = [
                'name.regex' => 'El nombre completo solo puede contener caracteres alfabéticos (A-Z, a-z).',
            ];
        }

        $request->validate($rules, $customMessages);

        $phonePrefix = $request->input('phone_prefix');
        if (!$phonePrefix) {
            $phonePrefix = ($country === 'MX') ? '+52' : '+57';
        }

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'country'      => $country,
            'state'        => $request->input('state', $country === 'MX' ? 'Puebla' : 'Cundinamarca'),
            'city'         => $request->input('city', $country === 'MX' ? 'Puebla' : 'Ubaté'),
            'postal_code'  => $request->input('postal_code'),
            'address'      => $request->input('address', ''),
            'phone'        => $request->input('phone', ''),
            'phone_prefix' => $phonePrefix,
        ]);

        if ($user->role === 'business') {
            $companyData = $request->only([
                'country', 'state', 'city', 'postal_code', 'tax_id', 'currency', 'phone_prefix', 'phone',
                'tipo_negocio', 'razon_social', 'nit', 'nombre_comercial', 'cedula_propietario',
                'habeas_data_accepted', 'address', 'nombre_representante', 'email_representante',
                'latitude', 'longitude', 'category', 'description'
            ]);
            $companyData['country'] = $country;
            $companyData['currency'] = $country === 'MX' ? 'MXN' : 'COP';
            $companyData['phone_prefix'] = $phonePrefix;
            $companyData['status'] = 'active';
            $companyData['user_id'] = $user->id;

            if ($country === 'MX') {
                $companyData['state'] = $request->input('state', 'Puebla');
                $companyData['city'] = $request->input('city', 'Puebla');
                $companyData['name'] = $request->input('nombre_comercial') ?: ($request->input('company_name') ?: ($request->input('razon_social') ?: $user->name));
                // RFC Opcional: Si viene vacío o con dummy, almacenar null sin inventar datos
                $rawTaxId = trim((string) $request->input('tax_id'));
                $companyData['tax_id'] = ($rawTaxId !== '' && $rawTaxId !== '000000' && strtoupper($rawTaxId) !== 'N/A') ? $rawTaxId : null;
                $companyData['category'] = $request->input('category', 'General');
                $companyData['description'] = $request->input('description', '');
                $companyData['tipo_negocio'] = $request->input('tipo_negocio', 'informal');
                $companyData['formalization'] = $request->input('tipo_negocio', 'informal');
            } else {
                $companyData['state'] = $request->input('state') ?: 'Cundinamarca';
                $companyData['city'] = $request->input('city') ?: 'Ubaté';
                if (($companyData['tipo_negocio'] ?? '') === 'formal') {
                    $companyData['name'] = $companyData['razon_social'] ?? $user->name;
                } else {
                    $companyData['name'] = $companyData['nombre_comercial'] ?? $user->name;
                }
            }

            if ($request->hasFile('camara_comercio_file')) {
                $companyData['camara_comercio_file'] = $request->file('camara_comercio_file')->store('documents', 'public');
            }
            if ($request->hasFile('rut_file')) {
                $companyData['rut_file'] = $request->file('rut_file')->store('documents', 'public');
            }
            if ($request->hasFile('rut_personal_file')) {
                $companyData['rut_personal_file'] = $request->file('rut_personal_file')->store('documents', 'public');
            }
            if ($request->hasFile('logo')) {
                $companyData['logo'] = $request->file('logo')->store('logos', 'public');
            }

            \App\Models\Company::create($companyData);
        }

        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session()->put('google_pending', [
            'name'       => $user->name,
            'email'      => $user->email,
            'google_id'  => null,
            'avatar'     => null,
            'code'       => $newCode,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(
            new \App\Mail\GoogleVerificationMail($newCode, $user->name)
        );

        return redirect()->route('verify.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
