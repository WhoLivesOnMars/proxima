<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Sprint;

class EpicStoreRequest extends FormRequest
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
        $projet = $this->route('projet');

        return [
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('epic', 'nom')
                    ->where(fn($q) => $q->where('id_projet', $projet->id_projet)),
            ],
            'id_sprint' => [
                'required',
                Rule::exists('sprint', 'id_sprint')
                    ->where(fn($q) => $q->where('id_projet', $projet->id_projet)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'An epic with this name already exists in this project.',
            'id_sprint.required' => 'Please select a sprint.',
            'id_sprint.exists' => 'The selected sprint does not belong to this project.',
        ];
    }
}
