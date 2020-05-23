<?php

namespace App\Exceptions;

use Exception;
use http\Client\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;

class ApiExceptionHandler extends Exception
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
        return [];
    }

    /**
     * Set the Errors.
     *
     * @param Validator $validator
     * @return Response
     */
    protected function failedValidation(Validator $validator)
    {
        $route_name = str_replace(' ', '_', strtoupper(Route::currentRouteName()));
        $failed_rules = $validator->getMessageBag()->toArray();
        $rule = $validator->failed();
        $errors = [];
        foreach ($failed_rules as $error_name => $error_message) {
            $attribute = array_keys($rule["$error_name"]);
            $errors[] = [
                "code" => $route_name . '-' . strtoupper($error_name) . '-' . strtoupper($attribute[0]),
                "message" => $error_message[0]
            ];
        }
        $response = [
            "status" => 'validation failed',
            "error" => $errors
        ];
        throw new HttpResponseException(response()->json($response, 422));
    }
}
