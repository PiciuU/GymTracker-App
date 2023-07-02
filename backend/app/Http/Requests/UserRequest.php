<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;

class UserRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status' => "Error",
            'message' => "Validation failed. Please check the following fields:",
            'data' => $validator->errors(),
        ], 422);

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    protected function hasAdminPrivileges(): bool
    {
        if ($this->user() && $this->user()->hasAdminPrivileges()) return true;

        return false;
    }

    protected function getMethodName(): string
    {
        $action = $this->route()->getActionMethod();
        return Str::camel($action);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $methodName = $this->getMethodName();

        if ($methodName === 'login') {
            return $this->login();
        } elseif ($methodName === 'register') {
            return $this->register();
        } elseif ($methodName === 'recover') {
            return $this->recover();
        } elseif ($methodName === 'resetPassword') {
            return $this->reset();
        } elseif ($methodName === 'updateName') {
            return $this->updateName();
        } elseif ($methodName === 'updateMail') {
            return $this->updateMail();
        } elseif ($methodName === 'updatePassword') {
            return $this->updatePassword();
        }
    }

    /**
     * Get the validation rules for login.
     *
     * @return array
     */
    protected function login() : array {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string']
        ];
    }

    /**
     * Get the validation rules for user registration.
     *
     * @return array
     */
    protected function register() : array {
        return [
            'login' => ['required', 'string', 'min:2', 'max:32', 'regex:/^[a-z0-9_]*$/i', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email'=> ['required', 'email', 'min:3', 'max:255', 'unique:users'],
            'password' => ['required', 'string']
        ];
    }

    /**
     * Get the validation rules for recover.
     *
     * @return array
     */
    protected function recover() : array {
        return [
            'email'=> ['required', 'email']
        ];
    }

    /**
     * Get the validation rules for reset.
     *
     * @return array
     */
    protected function reset() : array {
        return [
            'hash'=> ['required', 'string'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }

    /**
     * Get the validation rules for updating the user's name.
     *
     * @return array
     */
    protected function updateName() : array {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation rules for updating the user's mail.
     *
     * @return array
     */
    protected function updateMail() : array {
        return [
            'email' => ['sometimes', 'required', 'email', 'min:3', 'max:255', 'unique:users'],
        ];
    }

    /**
     * Get the validation rules for updating the user's password.
     *
     * @return array
     */
    protected function updatePassword() : array {
        return [
            'password_current' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() {
        if ($this->isMethod('POST')) {
            $this->merge([
                'name' => $this->login
            ]);
        }

        if ($this->filled('passwordConfirmation')) {
            $this->merge([
                'password_confirmation' => $this->passwordConfirmation
            ]);
        }

        if ($this->filled('passwordCurrent')) {
            $this->merge([
                'password_current' => $this->passwordCurrent
            ]);
        }
    }
}
