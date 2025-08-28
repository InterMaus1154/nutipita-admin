<div
    {{$attributes->merge([
    'class' => "sm:max-w-[var(--form-field-width)] flex flex-col gap-1"
])}}>
    {{$slot}}
</div>
