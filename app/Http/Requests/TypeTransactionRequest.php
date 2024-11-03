<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypeTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom_type_transa' => 'required|string|max:255|unique:type_transactions,nom_type_transa,' . 
                                 ($this->typeTransaction ? $this->typeTransaction->id_type_transa : null) . 
                                 ',id_type_transa'
        ];
    }

    public function messages()
    {
        return [
            'nom_type_transa.required' => 'Le nom du type de transaction est obligatoire',
            'nom_type_transa.unique' => 'Ce type de transaction existe déjà'
        ];
    }
}