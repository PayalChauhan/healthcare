<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    /**
     * Validation rules for user registration request
     *
     * @author Payal
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}