@props([
    'stashable' => null,
    'sticky' => null,
])

@php
    $classes = Flux::classes('[grid-area:sidebar]')
        ->add('z-1 flex flex-col gap-4 [:where(&)]:w-64 p-4');

    if ($sticky) {
        $attributes = $attributes->merge([
            'x-bind:style' => '{ position: \'sticky\', top: $el.offsetTop + \'px\', \'max-height\': \'calc(100dvh - \' + $el.offsetTop + \'px)\' }',
            'class' => 'max-h-dvh overflow-y-auto overscroll-contain',
        ]);
    }

    if ($stashable) {
        $attributes = $attributes->merge([
            // Always stashable — no breakpoint check
            'x-init' => '$el.classList.add(\'-translate-x-full\', \'rtl:translate-x-full\', \'transition-transform\'); $el.removeAttribute(\'x-cloak\');',
            // Always considered "stashed" unless toggled
            'x-bind:data-stashed' => 'true',
        ])->class([
            // Hide by default, show when toggled
            'x-cloak:hidden',
            '[[data-show-stashed-sidebar]_&]:translate-x-0!',
            'z-20! data-stashed:start-0! data-stashed:fixed! data-stashed:top-0! data-stashed:min-h-dvh! data-stashed:max-h-dvh!'
        ]);
    }
@endphp

@if ($stashable)
    <flux:sidebar.backdrop />
@endif

<div {{ $attributes->class($classes) }}
     x-data
     x-cloak
     data-flux-sidebar>
    {{ $slot }}
</div>
