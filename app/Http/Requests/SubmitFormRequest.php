<?php

namespace App\Http\Requests;

use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitFormRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'currency_in' => [
                'required',
                'exists:' . Currency::class . ',ticker',
            ],
            'currency_out' => [
                'required',
                'exists:' . Currency::class . ',ticker',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'source' => [
                'required',
                Rule::in(array_keys(config('exchange.sources'))),
            ],
            'method' => [
                'required',
                Rule::in(array_keys(config('exchange.methods'))),
            ],
        ];
    }

}
