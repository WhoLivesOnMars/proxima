<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SprintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $projectId = $this->route('projet')->id_projet ?? null;

        return [
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sprint')
                    ->where(fn ($q) => $q->where('id_projet', $projectId))
            ],
            'start_date' => ['required', 'date'],
            'duree' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
        ];
    }
}
