@props(['message' => ''])

<p
    {{ $attributes->merge(['class' => 'text-red-500 font-bold text-lg animate-shake']) }}
>{{ $message }}</p>
