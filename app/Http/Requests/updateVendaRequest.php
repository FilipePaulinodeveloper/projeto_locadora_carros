<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateVendaRequest extends FormRequest
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
            'id_veiculo' => 'required|exists:config_veiculos,id',
            'id_cliente' => 'required|exists:config_clientes,id',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|string|max:255',
            'status' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'id_veiculo.required' => 'O campo veículo é obrigatório.',
            'id_veiculo.exists' => 'O veículo selecionado não existe.',

            'id_cliente.required' => 'O campo cliente é obrigatório.',
            'id_cliente.exists' => 'O cliente selecionado não existe.',

            'valor.required' => 'O valor da venda é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior ou igual a 0.',

            'tipo.required' => 'O tipo da venda é obrigatório.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.max' => 'O tipo não pode ter mais de 255 caracteres.',

            'status.required' => 'O status da venda é obrigatório.',
            'status.string' => 'O status deve ser um texto.',
            'status.in' => 'O status deve ser "pendente", "aprovado" ou "rejeitado".',
        ];
    }
}
