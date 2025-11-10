<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Epic;
use App\Models\Sprint;

class EpicUpdateRequest extends FormRequest
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
        $epic = $this->route('epic');
        $idProjet = $epic->id_projet ?? $this->input('id_projet');

        return [
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('epic', 'nom')
                    ->where(fn($q) => $q->where('id_projet', $idProjet))
                    ->ignore($epic->id_epic, 'id_epic'),
            ],
            'id_sprint' => [
                'sometimes',
                'nullable',
                Rule::exists('sprint', 'id_sprint')
                    ->where(fn($q) => $q->where('id_projet', $idProjet)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'An epic with this name already exists in this project.',
            'id_sprint.exists' => 'The selected sprint must belong to the same project as this epic.',
        ];
    }
}
