<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class ExerciseRequest extends FormRequest
{
    const MUSCLE_GROUPS = [
        'Traps', 'Shoulders', 'Chest', 'Biceps', 'Forearms',
        'Obliques', 'Abdominals', 'Quads', 'Calves', 'Lats',
        'Triceps', 'Lower Back', 'Glutes', 'Hamstrings',
        'Warmup', 'Full Body', 'Legs'
    ];

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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'required'],
            'muscle_group' => ['sometimes', 'required', 'string', Rule::in(self::MUSCLE_GROUPS)],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'is_public' => ['required','integer', Rule::in([0,1])],
        ];
    }

    /**
     * Get the validation rules for updating a user exercise history.
     *
     * @return array
     */
    protected function update() : array {
        $rules = [
            'name' => ['sometimes','required','string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'muscle_group' => ['sometimes', 'string', Rule::in(self::MUSCLE_GROUPS)],
            'is_public' => ['sometimes', 'integer', Rule::in([0,1])],
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
