<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarVeiculoRequest extends FormRequest
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
            'tipo' => 'required|in:Carro,Moto',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'ano_fabricacao' => 'required|integer|digits:4|min:1900|max:' . date('Y'),
            'ano_modelo' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 1),
            'placa' => 'required|string|max:20|unique:config_veiculos,placa',
            'renavam' => 'required|string|max:20|unique:config_veiculos,renavam',
            'chassi' => 'required|string|max:50|unique:config_veiculos,chassi',
            'cor' => 'required|string|max:50',
            'quilometragem' => 'required|numeric|min:0',

            'combustivel' => 'required|in:Gasolina,Álcool,Flex,Diesel,Elétrico,Híbrido',
            'valor_venda' => 'nullable|numeric|min:0',
            'valor_diaria' => 'nullable|numeric|min:0',
            'disponivel_venda' => 'required',
            'status' => 'required',
            'disponivel_locacao' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'tipo.required' => 'O tipo do veículo é obrigatório.',
            'tipo.in' => 'O tipo deve ser Carro ou Moto.',
            'marca.required' => 'A marca é obrigatória.',
            'modelo.required' => 'O modelo é obrigatório.',
            'ano_fabricacao.required' => 'O ano de fabricação é obrigatório.',
            'ano_fabricacao.digits' => 'O ano de fabricação deve ter 4 dígitos.',
            'ano_fabricacao.min' => 'O ano de fabricação deve ser no mínimo 1900.',
            'ano_fabricacao.max' => 'O ano de fabricação não pode ser maior que o ano atual.',
            'ano_modelo.required' => 'O ano do modelo é obrigatório.',
            'ano_modelo.digits' => 'O ano do modelo deve ter 4 dígitos.',
            'ano_modelo.min' => 'O ano do modelo deve ser no mínimo 1900.',
            'ano_modelo.max' => 'O ano do modelo não pode ser maior que o próximo ano.',
            'placa.required' => 'A placa é obrigatória.',
            'placa.unique' => 'Esta placa já está cadastrada.',
            'renavam.required' => 'O RENAVAM é obrigatório.',
            'renavam.unique' => 'Este RENAVAM já está cadastrado.',
            'chassi.required' => 'O chassi é obrigatório.',
            'chassi.unique' => 'Este chassi já está cadastrado.',
            'cor.required' => 'A cor é obrigatória.',
            'quilometragem.required' => 'A quilometragem é obrigatória.',
            'quilometragem.numeric' => 'A quilometragem deve ser um número.',
            'combustivel.required' => 'O tipo de combustível é obrigatório.',
            'combustivel.in' => 'O combustível deve ser Gasolina, Álcool, Flex, Diesel, Elétrico ou Híbrido.',
            'valor_venda.numeric' => 'O valor de venda deve ser um número.',
            'valor_venda.min' => 'O valor de venda não pode ser negativo.',
            'valor_diaria.numeric' => 'O valor da diária deve ser um número.',
            'valor_diaria.min' => 'O valor da diária não pode ser negativo.',
            'disponivel_venda.required' => 'O campo disponível para venda é obrigatório.',
            'disponivel_locacao.required' => 'O campo disponível para locação é obrigatório.',
            'status.required' => 'O status do veículo é obrigatório.',

        ];
    }
}
