<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $country = strtoupper((string) $this->input('country', 'CO'));
        if ($country === 'MX') {
            $this->merge([
                'state' => $this->input('state') ?: 'Puebla',
                'city'  => $this->input('city') ?: 'Puebla',
            ]);
        } else {
            $this->merge([
                'state' => $this->input('state') ?: 'Cundinamarca',
                'city'  => $this->input('city') ?: 'Ubaté',
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $section = $this->input('section', 'all');
        $country = strtoupper((string) $this->input('country', 'CO'));
        $isMexico = ($country === 'MX');

        if ($section === 'general') {
            return [
                'name'        => ['required', 'string', 'max:255'],
                'country'     => ['nullable', 'in:CO,MX'],
                'state'       => ['nullable', 'string', 'max:100'],
                'city'        => ['nullable', 'string', 'max:100'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'currency'    => ['nullable', 'in:COP,MXN'],
                'description' => ['nullable', 'string'],
                'category'    => ['nullable', 'string', 'max:100'],
                'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'banner'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,bmp', 'max:15360'],
            ];
        }

        if ($section === 'legal') {
            if ($isMexico) {
                return [
                    'country'              => ['required', 'in:CO,MX'],
                    'tipo_negocio'         => ['nullable', 'in:formal,informal'],
                    'habeas_data_accepted' => ['accepted'],
                    'direccion_fisica'     => ['nullable', 'string', 'max:255'],
                    'address'              => ['nullable', 'string', 'max:255'],
                    'state'                => ['nullable', 'string', 'max:100'],
                    'city'                 => ['nullable', 'string', 'max:100'],
                    'postal_code'          => ['nullable', 'string', 'max:20'],
                    'tax_id'               => ['nullable', 'string', 'max:50'], // RFC Opcional
                    'razon_social'         => ['nullable', 'string', 'max:255'],
                    'nombre_comercial'     => ['nullable', 'string', 'max:255'],
                    'company_name'         => ['nullable', 'string', 'max:255'],
                    'nombre_representante' => ['nullable', 'string', 'max:255'],
                    'latitude'             => ['nullable', 'numeric'],
                    'longitude'            => ['nullable', 'numeric'],
                ];
            }

            return [
                'country'              => ['nullable', 'in:CO,MX'],
                'state'                => ['nullable', 'string', 'max:100'],
                'city'                 => ['nullable', 'string', 'max:100'],
                'tipo_negocio'         => ['required', 'in:formal,informal'],
                'habeas_data_accepted' => ['accepted'],
                'direccion_fisica'     => ['nullable', 'string', 'max:255'],
                'address'              => ['nullable', 'string', 'max:255'],
                'latitude'             => ['nullable', 'numeric'],
                'longitude'            => ['nullable', 'numeric'],
                'razon_social'         => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
                'nit'                  => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'regex:/^[0-9]+$/'],
                'camara_comercio_file' => ['required_if:tipo_negocio,formal', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
                'rut_file'             => ['required_if:tipo_negocio,formal', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
                'nombre_representante' => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
                'email_representante'  => ['required_if:tipo_negocio,formal', 'nullable', 'email', 'max:255'],
                'nombre_comercial'     => ['required_if:tipo_negocio,informal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
                'cedula_propietario'   => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
                'rut_personal_file'    => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            ];
        }

        if ($section === 'contact') {
            return [
                'phone'        => ['nullable', 'string', 'max:20'],
                'phone_prefix' => ['nullable', 'string', 'max:10'],
                'email'        => ['nullable', 'email'],
                'instagram'    => ['nullable', 'string', 'max:100'],
                'facebook'     => ['nullable', 'string', 'max:100'],
                'website'      => ['nullable', 'url'],
                'latitude'     => ['nullable', 'numeric'],
                'longitude'    => ['nullable', 'numeric'],
            ];
        }

        // Default (all / registro completo)
        if ($isMexico) {
            return [
                'name'                 => ['nullable', 'string', 'max:255'],
                'company_name'         => ['nullable', 'string', 'max:255'],
                'nombre_comercial'     => ['required_without_all:name,company_name,razon_social', 'nullable', 'string', 'max:255'],
                'country'              => ['required', 'in:CO,MX'],
                'currency'             => ['nullable', 'in:COP,MXN'],
                'state'                => ['nullable', 'string', 'max:100'],
                'city'                 => ['nullable', 'string', 'max:100'],
                'postal_code'          => ['nullable', 'string', 'max:20'],
                'tax_id'               => ['nullable', 'string', 'max:50'], // RFC Opcional
                'description'          => ['nullable', 'string'],
                'category'             => ['nullable', 'string', 'max:100'],
                'address'              => ['nullable', 'string', 'max:255'],
                'phone'                => ['nullable', 'string', 'max:20'],
                'phone_prefix'         => ['nullable', 'string', 'max:10'],
                'email'                => ['nullable', 'email'],
                'email_representante'  => ['nullable', 'email', 'max:255'],
                'instagram'            => ['nullable', 'string', 'max:100'],
                'facebook'             => ['nullable', 'string', 'max:100'],
                'website'              => ['nullable', 'url'],
                'logo'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
                'banner'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,bmp', 'max:2048'],
                'tipo_negocio'         => ['nullable', 'in:formal,informal'],
                'habeas_data_accepted' => ['accepted'],
                'direccion_fisica'     => ['nullable', 'string', 'max:255'],
                'latitude'             => ['nullable', 'numeric'],
                'longitude'            => ['nullable', 'numeric'],
                'razon_social'         => ['nullable', 'string', 'max:255'],
                'nombre_representante' => ['nullable', 'string', 'max:255'],
            ];
        }

        return [
            'name'                 => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'country'              => ['nullable', 'in:CO,MX'],
            'state'                => ['nullable', 'string', 'max:100'],
            'city'                 => ['nullable', 'string', 'max:100'],
            'description'          => ['nullable', 'string'],
            'category'             => ['nullable', 'string', 'max:100'],
            'address'              => ['nullable', 'string', 'max:255'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'email'                => ['nullable', 'email'],
            'instagram'            => ['nullable', 'string', 'max:100'],
            'facebook'             => ['nullable', 'string', 'max:100'],
            'website'              => ['nullable', 'url'],
            'logo'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'banner'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,bmp', 'max:2048'],
            'tipo_negocio'         => ['required', 'in:formal,informal'],
            'habeas_data_accepted' => ['accepted'],
            'direccion_fisica'     => ['nullable', 'string', 'max:255'],
            'latitude'             => ['nullable', 'numeric'],
            'longitude'            => ['nullable', 'numeric'],
            'razon_social'         => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'nit'                  => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'regex:/^[0-9]+$/'],
            'camara_comercio_file' => ['required_if:tipo_negocio,formal', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
            'rut_file'             => ['required_if:tipo_negocio,formal', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
            'nombre_representante' => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email_representante'  => ['required_if:tipo_negocio,formal', 'nullable', 'email', 'max:255'],
            'nombre_comercial'     => ['required_if:tipo_negocio,informal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'cedula_propietario'   => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'rut_personal_file'    => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required'                    => 'El nombre es obligatorio.',
            'name.regex'                       => 'El nombre solo puede contener letras (A-Z, a-z).',
            'nombre_comercial.required_without_all' => 'El nombre del emprendimiento es obligatorio.',
            'state.required'                   => 'El estado es obligatorio para comercios en México.',
            'city.required'                    => 'El municipio o ciudad es obligatorio para comercios en México.',
            'tipo_negocio.required'            => 'Debe seleccionar el tipo de negocio.',
            'habeas_data_accepted.accepted'    => 'Debe aceptar la política de tratamiento de datos personales / aviso de privacidad.',
            
            'razon_social.required_if'         => 'La razón social es obligatoria para empresas formales.',
            'razon_social.regex'               => 'La razón social solo puede contener letras (A-Z, a-z).',
            'nit.required_if'                  => 'El NIT es obligatorio para empresas formales en Colombia.',
            'nit.regex'                        => 'El NIT solo puede contener números (0-9).',
            'camara_comercio_file.required_if' => 'El certificado de Cámara de Comercio en formato PDF es obligatorio.',
            'camara_comercio_file.mimes'       => 'La Cámara de Comercio debe ser obligatoriamente un archivo en formato PDF.',
            'camara_comercio_file.max'         => 'El archivo de Cámara de Comercio no debe pesar más de 5MB.',
            'rut_file.required_if'             => 'El RUT en formato PDF es obligatorio para empresas formales.',
            'rut_file.mimes'                   => 'El archivo RUT debe ser obligatoriamente en formato PDF.',
            'rut_file.max'                     => 'El archivo RUT no debe pesar más de 5MB.',

            'nombre_comercial.required_if'     => 'El nombre comercial es obligatorio para emprendimientos informales.',
            'nombre_comercial.regex'           => 'El nombre comercial solo puede contener letras (A-Z, a-z).',
            'nombre_representante.required_if' => 'El nombre del representante legal es obligatorio.',
            'nombre_representante.regex'       => 'El nombre del representante legal solo puede contener letras (A-Z, a-z).',
            'email_representante.required_if'  => 'El correo del representante legal es obligatorio.',
            'cedula_propietario.required'      => 'La cédula del representante / propietario es obligatoria.',
            'cedula_propietario.regex'         => 'La cédula solo puede contener números (0-9).',
            'rut_personal_file.mimes'          => 'El RUT personal debe ser obligatoriamente en formato PDF.',
            'rut_personal_file.max'            => 'El RUT personal no debe pesar más de 5MB.',
        ];
    }
}
