<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\JSONRequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ImageUploadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => ['required','image','max:1024'],
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'IMAGE_REQUIRED',
            'image.image'    => 'IMAGE_BAD_FORMAT',
            'image.max'      => 'IMAGE_SIZE',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new JSONRequestException($validator);
    }
}
