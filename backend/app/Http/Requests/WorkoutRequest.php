<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkoutRequest extends FormRequest
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
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'day_of_week' => ['required', Rule::in([1,2,3,4,5,6,7])]
        ];
    }

    /**
     * Get the validation rules for updating a user exercise history.
     *
     * @return array
     */
    protected function update() : array {
        $rules = [
            'day_of_week' => ['required', Rule::in([1,2,3,4,5,6,7])]
        ];

        if ($this->hasAdminPrivileges()) {
            $rules = array_merge($rules, [
                'user_id' => ['sometimes', 'required', 'integer', Rule::exists('users', 'id')]
            ]);
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() {
        $this->merge([
            'day_of_week' => $this->dayOfWeek
        ]);
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
    }
}