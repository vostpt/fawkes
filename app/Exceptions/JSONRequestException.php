<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class JSONRequestException extends Exception
{
    protected $validator;

    protected $code = 422;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function render()
    {
        // return a json with desired format
        return response()->json(['status' => 'ERROR', 'errors' => $this->validator->errors()], $this->code);
    }
}
