<div >
    @unless(empty($modalStack))
        {{--backdrop--}}
        <div class="fixed inset-0 w-full h-full bg-black/60 z-200 flex justify-center items-center p-4" @click="$dispatch('modal-clear')">
            @foreach($modalStack as $index => $modal)
                @livewire($modal['component'], $modal['data'], $modal['key'])
            @endforeach
        </div>
    @endunless
</div>
