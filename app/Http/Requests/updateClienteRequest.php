<?php

namespace App\Http\Requests;

use App\Http\Controllers\ConfigClientes;
use App\Models\ConfigCliente;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class updateClienteRequest extends FormRequest
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
        $cliente = ConfigCliente::where('token', $this->id)->first();

        return [
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'max:15',
                Rule::unique('config_clientes', 'cpf')->ignore($cliente->id),
            ],
            'data_nascimento' => 'nullable|date',
            'telefone' => 'nullable|string|max:15',
            'email' => [
                'nullable',
                'email',
                Rule::unique('config_clientes', 'email')->ignore($cliente->id),
            ],
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
            'nome.required' => 'O campo Nome é obrigatório.',
            'nome.string' => 'O Nome deve ser uma string.',
            'nome.max' => 'O Nome deve ter no máximo 255 caracteres.',

            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser uma string.',
            'cpf.max' => 'O CPF deve ter no máximo 15 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado.',

            'data_nascimento.date' => 'A Data de Nascimento deve ser uma data válida.',

            'telefone.string' => 'O Telefone deve ser uma string.',
            'telefone.max' => 'O Telefone deve ter no máximo 15 caracteres.',

            'email.email' => 'O E-mail deve ser um endereço de e-mail válido.',
            'email.unique' => 'Este E-mail já está cadastrado.',

            'status.required' => 'O campo Status é obrigatório.',
            'status.boolean' => 'O campo Status deve ser verdadeiro ou falso.',

            'logradouro.required' => 'O campo Logradouro é obrigatório.',
            'logradouro.string' => 'O Logradouro deve ser uma string.',
            'logradouro.max' => 'O Logradouro deve ter no máximo 255 caracteres.',

            'numero.required' => 'O campo Número é obrigatório.',
            'numero.string' => 'O Número deve ser uma string.',
            'numero.max' => 'O Número deve ter no máximo 10 caracteres.',

            'complemento.string' => 'O Complemento deve ser uma string.',
            'complemento.max' => 'O Complemento deve ter no máximo 255 caracteres.',

            'bairro.required' => 'O campo Bairro é obrigatório.',
            'bairro.string' => 'O Bairro deve ser uma string.',
            'bairro.max' => 'O Bairro deve ter no máximo 255 caracteres.',

            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.string' => 'O CEP deve ser uma string.',
            'cep.max' => 'O CEP deve ter no máximo 9 caracteres.',

            'cidade.required' => 'O campo Cidade é obrigatório.',
            'cidade.string' => 'A Cidade deve ser uma string.',
            'cidade.max' => 'A Cidade deve ter no máximo 255 caracteres.',

            'estado.required' => 'O campo Estado é obrigatório.',
            'estado.string' => 'O Estado deve ser uma string.',
            'estado.max' => 'O Estado deve ter no máximo 2 caracteres.',
        ];
    }
}
