<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:15|unique:config_clientes,cpf',
            'data_nascimento' => 'nullable|date',
            'telefone' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:config_clientes,email',
            'status' => 'required|boolean',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cep' => 'required|string|max:9',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
        ];
    }

    public function messages()
{
    return [
        'nome.required' => 'O campo nome é obrigatório.',
        'nome.string' => 'O nome deve ser um texto.',
        'nome.max' => 'O nome não pode ter mais de 255 caracteres.',

        'cpf.required' => 'O campo CPF é obrigatório.',
        'cpf.string' => 'O CPF deve ser um texto.',
        'cpf.max' => 'O CPF não pode ter mais de 15 caracteres.',
        'cpf.unique' => 'Este CPF já está cadastrado.',

        'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',

        'telefone.string' => 'O telefone deve ser um texto.',
        'telefone.max' => 'O telefone não pode ter mais de 15 caracteres.',

        'email.email' => 'O e-mail deve ser um endereço de e-mail válido.',
        'email.unique' => 'Este e-mail já está cadastrado.',


        'status.required' => 'O campo status é obrigatório.',
        'status.boolean' => 'O campo status deve ser verdadeiro ou falso.',

        'logradouro.required' => 'O campo logradouro é obrigatório.',
        'logradouro.string' => 'O logradouro deve ser um texto.',
        'logradouro.max' => 'O logradouro não pode ter mais de 255 caracteres.',

        'numero.required' => 'O campo número é obrigatório.',
        'numero.string' => 'O número deve ser um texto.',
        'numero.max' => 'O número não pode ter mais de 10 caracteres.',

        'complemento.string' => 'O complemento deve ser um texto.',
        'complemento.max' => 'O complemento não pode ter mais de 255 caracteres.',

        'bairro.required' => 'O campo bairro é obrigatório.',
        'bairro.string' => 'O bairro deve ser um texto.',
        'bairro.max' => 'O bairro não pode ter mais de 255 caracteres.',

        'cep.required' => 'O campo CEP é obrigatório.',
        'cep.string' => 'O CEP deve ser um texto.',
        'cep.max' => 'O CEP não pode ter mais de 9 caracteres.',

        'cidade.required' => 'O campo cidade é obrigatório.',
        'cidade.string' => 'A cidade deve ser um texto.',
        'cidade.max' => 'A cidade não pode ter mais de 255 caracteres.',

        'estado.required' => 'O campo estado é obrigatório.',
        'estado.string' => 'O estado deve ser um texto.',
        'estado.max' => 'O estado deve ter no máximo 2 caracteres.',
    ];
}
}
