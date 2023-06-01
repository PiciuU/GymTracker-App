<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkoutExerciseRequest extends FormRequest
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

        // return [
        //     'sets' => ['integer'],
        //     'reps' => ['integer'],
        //     'rest_time' => ['integer'],
        //     'weight' => ['numeric', 'regex:/^\d{0,6}(\.\d{1,2})?$/']
        // ];
    }

     /**
     * Get the validation rules for storing a user exercise history.
     *
     * @return array
     */
    protected function store() : array {
        return [
            'sets' => ['sometimes','required','integer'],
            'reps' => ['sometimes','required','integer'],
            'rest_time' => ['sometimes','required','integer'],
            'weight' => ['sometimes','required', 'numeric', 'regex:/^\d{0,6}(\.\d{1,2})?$/'],
            'workout_id' => ['required', 'integer', Rule::exists('workouts', 'id')],
            'exercise_id' => ['required', 'integer', Rule::exists('exercises', 'id')]
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

        if ($this->hasAdminPrivileges()) {
            $rules = array_merge($rules, [
                'workout_id' => ['sometimes', 'required', 'integer', Rule::exists('workouts', 'id')],
                'exercise_id' => ['sometimes', 'required', 'integer', Rule::exists('exercises', 'id')]
            ]);
        }

        return $rules;
    }

    // public function validationData() {
    //     $segments = $this->segments();
    //     $workoutIndex = array_search('workout', $segments);
    //     $workoutSlug = $segments[$workoutIndex + 1];

    //     if ($this->isMethod('POST')) {
    //         return array_merge($this->all(), [
    //             'workout_id' => $workoutSlug,
    //         ]);
    //     }
    //     else {
    //         $exerciseIndex = array_search('exercise', $segments);
    //         $exerciseSlug = $segments[$exerciseIndex + 1];

    //         return array_merge($this->all(), [
    //             'workout_id' => $workoutSlug,
    //             'exercise_id' => $exerciseSlug
    //         ]);
    //     }
    // }

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
            $this->merge([
                'rest_time' => $this->restTime
            ]);
        }
    }
}
