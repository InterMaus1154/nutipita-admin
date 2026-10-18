<x-flux-layout>
    @push('react')
        @viteReactRefresh
        @vite(['client/main.tsx'])
        <div id="react-root"></div>
    @endpush
</x-flux-layout>
