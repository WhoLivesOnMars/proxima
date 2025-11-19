<x-mail::message>
# Project invitation

@if($project && $project->owner)
{{ $project->owner->prenom }} {{ $project->owner->nom }} has invited you to join the project **{{ $project->nom }}**.
@else
You have been invited to join the project **{{ $project->nom ?? 'Project' }}**.
@endif

@if(!empty($project->description))
> {{ $project->description }}
@endif

<x-mail::button :url="$acceptUrl">
Join the project
</x-mail::button>

If you don’t have an account yet, you’ll be asked to register first.
After logging in, you will be automatically added to this project.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
