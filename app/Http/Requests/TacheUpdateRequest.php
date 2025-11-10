<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Tache;
use App\Models\Sprint;
use App\Models\Epic;
use Carbon\Carbon;

class TacheUpdateRequest extends FormRequest
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
        $tache = $this->route('tache');
        return [
            'id_sprint' => ['required','exists:sprint,id_sprint'],
            'id_epic' => ['nullable','exists:epic,id_epic'],
            'id_utilisateur' => ['sometimes','nullable','exists:utilisateur,id_utilisateur'],
            'titre' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_date' => ['nullable','date'],
            'deadline' => ['nullable','date'],
            'status' => ['required', Rule::in(['todo','in_progress','done'])],
        ];
    }

    public function withValidator($v)
    {
        $v->after(function ($v) {
            $tache = $this->route('tache');
            $pid = $tache->id_projet;

            $sOk = Sprint::where('id_sprint',$this->id_sprint)
                ->where('id_projet',$pid)->exists();
            if (!$sOk) {
                $v->errors()->add('id_sprint','Le sprint doit appartenir au même projet que la tâche.');
            }

            if ($this->filled('id_epic')) {
                $eOk = Epic::where('id_epic',$this->id_epic)
                    ->where('id_projet',$pid)->exists();
                if (!$eOk) {
                    $v->errors()->add('id_epic','L’épopée doit appartenir au même projet que la tâche.');
                }
            }

            if ($this->filled('deadline')) {
                $sprint = Sprint::find($this->id_sprint);
                if ($sprint) {
                    $start = Carbon::parse($sprint->start_date);
                    $end = $start->copy()->addDays($sprint->duree);

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
}
