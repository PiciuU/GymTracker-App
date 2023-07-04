<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class WorkoutExerciseRequest extends FormRequest
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
            'workout_id' => ['required', 'integer', Rule::exists('workouts', 'id')],
            'exercise_id' => ['required', 'integer', Rule::exists('exercises', 'id')],
            'sets' => ['sometimes', 'required', 'integer'],
            'reps' => ['sometimes', 'required', 'integer'],
            'rest_time' => ['sometimes', 'nullable', 'integer'],
            'weight' => ['sometimes', 'nullable', 'numeric', 'regex:/^\d{0,6}(\.\d{1,2})?$/'],
        ];
    }

    /**
     * Get the validation rules for updating a user exercise history.
     *
     * @return array
     */
    protected function update() : array {
        $rules = [
            'sets' => ['sometimes', 'nullable', 'integer'],
            'reps' => ['sometimes', 'nullable', 'integer'],
            'rest_time' => ['sometimes', 'nullable', 'integer'],
            'weight' => ['sometimes','nullable', 'numeric', 'regex:/^\d{0,6}(\.\d{1,2})?$/'],
        ];

        if ($this->user()?->hasAdminPrivileges()) {
            $rules += [
                'workout_id' => ['sometimes', 'required', 'integer', Rule::exists('workouts', 'id')],
                'exercise_id' => ['sometimes', 'required', 'integer', Rule::exists('exercises', 'id')]
            ];
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation() {
        if ($this->isMethod('POST')) {
            $segments = $this->segments();
            $workoutIndex = array_search('workout', $segments);
            $workoutSlug = $segments[$workoutIndex + 1];
            $this->merge([
                'workout_id' => $workoutSlug,
                'exercise_id' => $this->exerciseId
            ]);
        }

        if (!$this->isMethod('POST') && $this->filled('workoutId')) {
            $this->merge([
                'workout_id' => $this->workoutId
            ]);
        }

        if (!$this->isMethod('POST') && $this->filled('exerciseId')) {
            $this->merge([
                'exercise_id' => $this->exerciseId
            ]);
        }

        if ($this->filled('restTime')) {
            $restTime = $this->restTime;

            if (preg_match('/^\d{2}:\d{2}$/', $restTime)) {
                $parts = explode(':', $restTime);
                $minutes = $parts[0];
                $seconds = $parts[1];

                $restTime = ($minutes * 60) + $seconds;
            }

            $this->merge([
                'rest_time' => $restTime
            ]);
        }
    }
}
