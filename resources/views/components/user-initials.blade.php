@props([
    'first' => '',
    'last'  => '',
    'class' => '',
])

@php
    $f = trim((string)$first);
    $l = trim((string)$last);

    $fi = $f !== '' ? mb_strtoupper(mb_substr($f, 0, 1)) : '';
    $li = $l !== '' ? mb_strtoupper(mb_substr($l, 0, 1)) : '';

    $initials = $fi . $li;
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full bg-emerald-600 text-white font-semibold $class"]) }}
    aria-label="User initials"
    title="{{ trim($first.' '.$last) }}">
    {{ $initials ?: '??' }}
</div>
