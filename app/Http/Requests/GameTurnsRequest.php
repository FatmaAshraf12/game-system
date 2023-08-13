<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GameTurnsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'playersNum' => 'nullable|numeric|max:26|min:2',
            'turnsNum' => 'nullable|numeric|min:1',
            'firstPlayer' => 'nullable|regex:/^[a-zA-Z]{1,1}$/'
        ];
    }


    public function messages()
    {
        return [
            'playersNum.numeric' => 'playersNum should be number',
            'playersNum.max' => "playersNum shouldn't be greater than 26",
            'playersNum.min' => "playersNum shouldn't be smaller than 2",
            'turnsNum.min' => "turnsNum shouldn't be smaller than 1",
            'turnsNum.numeric' => 'turnsNum should be number',
            'firstPlayer.regex' => 'firstPlayer should be one character from A to Z'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            [
                'message' => 'Failed',
                'errors' => $validator->errors()
            ],
            422
        ));
    }
}
