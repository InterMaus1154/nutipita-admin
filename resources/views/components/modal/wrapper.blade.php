@props([
    'size' => 'full',
    'title' => '',
    'content' => null,
    'footer' => null
])

@php
    $sizes = [
        '3xs'    => 'w-3xs',
        '2xs'    => 'w-2xs',
        'xs'     => 'w-xs',
        'sm'     => 'w-sm',
        'md'     => 'w-md',
        'lg'     => 'w-lg',
        'xl'     => 'w-xl',
        '2xl'    => 'w-2xl',
        '3xl'    => 'w-3xl',
        '4xl'    => 'w-4xl',
        '5xl'    => 'w-5xl',
        '6xl'    => 'w-6xl',
        '7xl'    => 'w-7xl',
        'auto'   => 'w-auto',
        'px'     => 'w-px',
        'full'   => 'w-full',
        'screen' => 'w-screen',
        'dvw'    => 'w-dvw',
        'dvh'    => 'w-dvh',
        'lvw'    => 'w-lvw',
        'lvh'    => 'w-lvh',
        'svw'    => 'w-svw',
        'svh'    => 'w-svh',
        'min'    => 'w-min',
        'max'    => 'w-max',
        'fit'    => 'w-fit',
    ];

    $sectionPadding = 'p-4';

@endphp

{{--modal skeleton--}}
<div class="{{$sizes[$size]}} bg-slate-700 rounded-md divide-y divide-slate-500" @click.stop>

    <header class="relative {{$sectionPadding}}">
        <h1 class="text-4xl text-center font-bold text-accent">{{$title}}</h1>
        <flux:button @click="$dispatch('modal-close')" variant="primary" class="absolute! right-2 top-2">X</flux:button>
    </header>

    @if(isset($content))
        <div class="{{$sectionPadding}}">
            {{$content}}
        </div>
    @endif

    @if(isset($footer))
        <footer class="{{$sectionPadding}}">
            {{$footer}}
        </footer>
    @endif
</div>
