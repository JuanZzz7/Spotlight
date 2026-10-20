<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Paso 1: Información de Contacto y Facturación
            'first_name'                 => 'required|string|max:100',
            'last_name'                  => 'required|string|max:100',
            'email'                      => 'required|email|max:150',
            'phone'                      => 'required|string|min:7|max:25',
            'doc_type'                   => 'required|string|in:CC,NIT,CE,PP,TI,RFC,CURP,INE',
            'doc_number'                 => 'required|string|min:4|max:30',

            // Paso 2: Dirección de Envío
            'country'                    => 'required|string|in:CO,Colombia,MX,México,Mexico',
            'department'                 => 'required|string|max:100',
            'city'                       => 'required|string|max:100',
            'neighborhood'               => 'nullable|string|max:100',
            'postal_code'                => 'nullable|string|max:20',
            'address'                    => 'required|string|max:255',
            'complement'                 => 'nullable|string|max:100',
            'delivery_notes'             => 'nullable|string|max:500',
            'different_billing_address'  => 'nullable|boolean',
            'billing_address'            => 'nullable|string|max:255',

            // Paso 3: Método de Pago y Datos Específicos
            'payment_method'             => 'nullable|string|in:card,pse,qr,wompi,mercadopago,spei,oxxo',

            // Tarjeta de Crédito / Débito
            'card_name'                  => 'required_if:payment_method,card|nullable|string|max:100',
            'card_number'                => 'required_if:payment_method,card|nullable|string|max:25',
            'exp_date'                   => 'required_if:payment_method,card|nullable|string|max:10',
            'cvv'                        => 'required_if:payment_method,card|nullable|string|max:4',
            'installments'               => 'nullable|integer|min:1|max:36',

            // PSE
            'pse_bank'                   => 'required_if:payment_method,pse|nullable|string|max:100',
            'pse_person_type'            => 'nullable|string|in:natural,juridica',
            'pse_doc_type'               => 'nullable|string|in:CC,NIT,CE,PP,TI',
            'pse_doc_number'             => 'nullable|string|max:30',
            'pse_email'                  => 'required_if:payment_method,pse|nullable|email|max:150',

            // Paso 4: Aceptación Legal
            'terms_accepted'             => 'accepted',
            'privacy_accepted'           => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'       => 'El nombre del comprador es obligatorio.',
            'last_name.required'        => 'Los apellidos son obligatorios.',
            'email.required'            => 'El correo electrónico es obligatorio para recibir el comprobante.',
            'email.email'               => 'Ingresa un correo electrónico válido.',
            'phone.required'            => 'El teléfono celular es requerido para la coordinación del envío.',
            'doc_type.required'         => 'Selecciona el tipo de documento.',
            'doc_number.required'       => 'El número de documento es obligatorio para facturación y despacho.',
            'country.required'          => 'El país es obligatorio.',
            'department.required'       => 'El departamento es obligatorio.',
            'city.required'             => 'El municipio o ciudad de entrega es obligatorio.',
            'address.required'          => 'La dirección física detallada de entrega es obligatoria.',
            'terms_accepted.accepted'   => 'Debes aceptar los Términos y Condiciones para continuar.',
            'privacy_accepted.accepted' => 'Debes autorizar la política de Tratamiento de Datos Personales.',

            'card_name.required_if'     => 'Ingresa el nombre del titular tal como aparece en la tarjeta.',
            'card_number.required_if'   => 'Ingresa el número de la tarjeta.',
            'exp_date.required_if'      => 'Ingresa la fecha de vencimiento (MM/AA).',
            'cvv.required_if'           => 'Ingresa el código de seguridad CVV.',
            'pse_bank.required_if'      => 'Debes seleccionar tu banco para continuar con PSE.',
            'pse_email.required_if'     => 'Ingresa el correo electrónico registrado en PSE.',
        ];
    }
}
