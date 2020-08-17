<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\JSONRequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ImageGetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uuid' => ['required','uuid'],
        ];
    }

    public function messages()
    {
        return [
            'uuid.required' => 'UUID_REQUIRED',
            'uuid.uuid'     => 'UUID_NOT_UUID',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new JSONRequestException($validator);
    }
}
