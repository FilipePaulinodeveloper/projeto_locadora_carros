<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarFuncionarioRequest extends FormRequest
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

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:15|unique:config_funcionarios,cpf',
            'salario' => 'required|numeric|min:0',
            'cargo' => 'nullable',
            'status' => 'required|boolean',
        ];
    }

    // Defina as mensagens de erro personalizadas
    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'O CPF já está cadastrado.',
            'salario.required' => 'O salário é obrigatório.',
            'salario.numeric' => 'O salário deve ser numérico.',
            'status.required' => 'O status é obrigatório.',
            'deleted.required' => 'O campo de exclusão é obrigatório.',
        ];
    }
}
