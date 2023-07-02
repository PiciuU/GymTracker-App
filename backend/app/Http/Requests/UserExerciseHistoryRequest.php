<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class UserExerciseHistoryRequest extends FormRequest
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
        return $this->user()->tokenCan('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    /**
     * Get the validation rules for storing a user exercise history.
     *
     * @return array
     */
    protected function store() : array {
        return [
            'exercise_id' => ['required', 'integer', Rule::exists('exercises', 'id')],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'weight' => ['required','numeric', 'regex:/^\d{0,6}(\.\d{1,2})?$/'],
            'date' => ['required', 'date']
        ];
    }

    /**
     * Get the validation rules for updating a user exercise history.
     *
     * @return array
     */
    protected function update() : array {
        $rules = [
            'weight' => ['numeric', 'regex:/^\d{0,6}(\.\d{1,2})?$/'],
            'date' => ['date'],
        ];

        if ($this->hasAdminPrivileges()) {
            $rules = array_merge($rules, [
                'exercise_id' => ['sometimes', 'required', 'integer', Rule::exists('exercises', 'id')],
                'user_id' => ['sometimes', 'required', 'integer', Rule::exists('users', 'id')],
            ]);
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() {
        if ($this->isMethod('POST')) {
            $this->merge([
                'user_id' => $this->user()->id,
            ]);
        }
        else if ($this->filled('userId')) {
            $this->merge([
                'user_id' => $this->userId,
            ]);
        }

        if ($this->filled('exerciseId')) {
            $this->merge([
                'exercise_id' => $this->exerciseId,
            ]);
        }
    }
}
