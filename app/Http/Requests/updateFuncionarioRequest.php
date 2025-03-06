<?php

namespace App\Http\Requests;

use App\Models\ConfigFuncionario;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class updateFuncionarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {

        $funcionario = ConfigFuncionario::where('token', $this->id)->first();

        return [
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'max:15',
                Rule::unique('config_funcionarios', 'cpf')->ignore($funcionario->id),
            ],
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
            'salario.required' => 'O salário é obrigatório.',
            'status.required' => 'O status é obrigatório.',
            'deleted.required' => 'O campo de exclusão é obrigatório.',
        ];
    }
}
