<x-flux-layout>
    <x-page-section>
        <flux:badge color="red" icon="exclamation-triangle" size="lg">
            @if(session()->has('error_message'))
                {{session()->get('error_message')}}
            @else
                Route not found!
            @endif
        </flux:badge>
    </x-page-section>
</x-flux-layout>
