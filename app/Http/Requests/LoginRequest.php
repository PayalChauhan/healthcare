<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * Validation rules for user login
     *
     * @author Payal
     * @return array
     */
    public function rules() {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }
}