@props(['center' => null])
<div class="max-w-sm flex flex-col gap-1 {{$center ? "items-center" : "items-start"}}">
    {{$slot}}
</div>
