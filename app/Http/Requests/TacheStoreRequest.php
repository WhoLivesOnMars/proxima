<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Sprint;
use App\Models\Epic;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TacheStoreRequest extends FormRequest
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
        $pid    = $projet->id_projet;

        return [
            'id_sprint' => [
                'required',
                Rule::exists('sprint', 'id_sprint')
                    ->where(fn($q) => $q->where('id_projet', $pid)),
            ],
            'id_epic' => [
                'nullable',
                Rule::exists('epic', 'id_epic')
                    ->where(fn($q) => $q->where('id_projet', $pid)),
            ],
            'id_utilisateur' => ['nullable','exists:utilisateur,id_utilisateur'],
            'titre' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_date' => ['nullable','date'],
            'deadline' => ['nullable','date'],
            'status'  => ['required', Rule::in(['todo','in_progress','done'])],
        ];
    }

    public function withValidator($v)
    {
        $v->after(function ($v) {
            $projet = $this->route('projet');
            $pid = $projet->id_projet;

            if ($this->filled('id_epic')) {
                $inSprint = DB::table('epic_sprint')
                    ->where('id_epic', $this->id_epic)
                    ->where('id_sprint', $this->id_sprint)
                    ->exists();

                if (!$inSprint) {
                    $v->errors()->add('id_epic', 'The selected epic does not belong to the chosen sprint.');
                }
            }

            if ($this->filled('deadline')) {
                $sprint = Sprint::find($this->id_sprint);
                if ($sprint) {
                    $start = Carbon::parse($sprint->start_date);
                    $end   = $start->copy()->addWeeks((int)$sprint->duree);

                    if ($this->filled('start_date')) {
                        $st = Carbon::parse($this->start_date);
                        if ($st->lt($start) || $st->gt($end)) {
                            $v->errors()->add('start_date', 'Start date must be within the sprint dates.');
                        }
                    }

                    if ($this->filled('deadline')) {
                        $dl = Carbon::parse($this->deadline);
                        if ($dl->lt($start) || $dl->gt($end)) {
                            $v->errors()->add('deadline', 'The deadline must be within the sprint dates.');
                        }
                    }

                    if ($this->filled('start_date') && $this->filled('deadline')) {
                        if (Carbon::parse($this->start_date)->gt(Carbon::parse($this->deadline))) {
                            $v->errors()->add('start_date', 'Start date must be before or equal to deadline.');
                        }
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_sprint.required' => 'Please select a sprint.',
            'id_sprint.exists' => 'The selected sprint does not belong to this project.',
            'id_epic.exists' => 'The selected epic does not belong to this project.',
        ];
    }
}
