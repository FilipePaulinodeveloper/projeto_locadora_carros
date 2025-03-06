<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarManutencaoRequest extends FormRequest
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
            'id_veiculo' => 'required',
            'data_manutencao' => 'required|date',
            'descricao' => 'required|string|max:1000',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|in:Preventiva,Corretiva',
            'status' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'id_veiculo.required' => 'O campo id do veículo é obrigatório.',
            'id_veiculo.exists' => 'O veículo informado não existe.',
            'data_manutencao.required' => 'A data da manutenção é obrigatória.',
            'data_manutencao.date' => 'A data da manutenção deve ser uma data válida.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.min' => 'O valor deve ser maior ou igual a zero.',
            'tipo.required' => 'O tipo de manutenção é obrigatório.',
            'tipo.in' => 'O tipo de manutenção deve ser Preventiva ou Corretiva.',
            'token.required' => 'O token é obrigatório.',
            'token.string' => 'O token deve ser uma string.',
            'token.unique' => 'O token já está em uso.',
            'status.required' => 'O status é obrigatório.',
            'status.boolean' => 'O status deve ser verdadeiro ou falso.'
        ];
    }
}
