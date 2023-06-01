<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
        } elseif ($methodName === 'updateName') {
            return $this->updateName();
        } elseif ($methodName === 'updatePassword') {
            return $this->updatePassword();
        }

        if ($this->hasAdminPrivileges() && $methodName === 'update') {
            return $this->update();
        }
    }

    /**
     * Get the validation rules for login.
     *
     * @return array
     */
    protected function login() : array {
        return [
            'login' => ['required'],
            'password' => ['required']
        ];
    }

    /**
     * Get the validation rules for user registration.
     *
     * @return array
     */
    protected function register() : array {
        return [
            'login' => ['required', 'unique:users'],
            'name' => ['required'],
            'email'=> ['required', 'email', 'unique:users'],
            'password' => ['required'],
            'user_role_id' => ['required', Rule::in([1])],
        ];
    }

    /**
     * Get the validation rules for updating user information.
     *
     * @return array
     */
    protected function update() : array {
        return [
            'name' => ['sometimes', 'required'],
            'email'=> ['sometimes', 'required', 'email', 'unique:users'],
            'login' => ['sometimes', 'required', 'unique:users'],
            'user_role_id' => ['sometimes', 'required', 'integer'],
            'password' => ['sometimes', 'required']
        ];
    }

    /**
     * Get the validation rules for updating the user's name.
     *
     * @return array
     */
    protected function updateName() : array {
        return [
            'name' => ['sometimes', 'required'],
        ];
    }

    /**
     * Get the validation rules for updating the user's password.
     *
     * @return array
     */
    protected function updatePassword() : array {
        return [
            'password_current' => ['required'],
            'password' => ['required', 'confirmed'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() {
        if ($this->isMethod('POST')) {
            $this->merge([
                'user_role_id' => 1
            ]);
        }
        else if ($this->filled('userRoleId')) {
            $this->merge([
                'user_role_id' => $this->userRoleId
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
