<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExerciseRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['sometimes', 'required'],
            'muscle_group' => ['sometimes', 'required'],
            'thumbnail_url' => ['sometimes', 'required'],
            'attachment_url' => ['sometimes', 'required'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'is_public' => ['required','integer', Rule::in([0,1])],
            'is_approved' => ['required','integer', Rule::in([0,1])],
        ];
    }

    /**
     * Get the validation rules for updating a user exercise history.
     *
     * @return array
     */
    protected function update() : array {
        $rules = [
            'name' => ['sometimes','required','string'],
            'description' => ['sometimes'],
            'muscle_group' => ['sometimes'],
            'thumbnail_url' => ['sometimes'],
            'attachment_url' => ['sometimes'],
            'is_public' => ['sometimes','integer', Rule::in([0,1])],
        ];

        if ($this->hasAdminPrivileges()) {
            $rules = array_merge($rules, [
                'user_id' => ['sometimes', 'required', 'integer'],
                'is_approved' => ['sometimes', 'integer', Rule::in([0,1])]
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

        if ($this->isMethod('POST')) {
            $this->merge([
                'is_approved' => $this->hasAdminPrivileges(),
            ]);
        }
        else if ($this->hasAdminPrivileges() && $this->filled('isApproved')) {
            $this->merge([
                'is_approved' => $this->isApproved,
            ]);
        }

        if ($this->filled('muscleGroup')) {
            $this->merge([
                'muscle_group' => $this->muscleGroup
            ]);
        }
        if ($this->filled('thumbnailUrl')) {
            $this->merge([
                'thumbnail_url' => $this->thumbnailUrl
            ]);
        }
        if ($this->filled('attachmentUrl')) {
            $this->merge([
                'attachment_url' => $this->attachmentUrl
            ]);
        }
        if ($this->filled('isPublic')) {
            $this->merge([
                'is_public' => $this->isPublic
            ]);
        }
    }
}
