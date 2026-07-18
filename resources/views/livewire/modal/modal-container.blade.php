<div>
    @unless(empty($modalStack))
        {{--backdrop--}}
        <div class="fixed inset-0 w-full h-full bg-black/30 z-200 justify-center items-start p-4 overflow-y-auto"
             @mousedown="if($event.target == $el) $dispatch('modal-clear')" @click.self="$dispatch('modal-close')">
            @foreach($modalStack as $index => $modal)
                @php
                    $hasMoreThanOne = count($modalStack) > 1;
                @endphp
                @if($index === 0)
                    <div class="fixed inset-0 flex justify-center items-start p-4 overflow-y-auto"
                         style="z-index: {{200 + $index}}"  @click.self="$dispatch('modal-close')">
                        @livewire($modal['component'], $modal['data'], $modal['key'])
                    </div>
                @else
                    <div class="fixed inset-0 w-full h-full bg-white/60" style="z-index: {{200+$index}}"  @click.self="$dispatch('modal-close')">
                        <div class="fixed inset-0 flex justify-center items-start p-4 overflow-y-auto"
                             style="z-index: {{200 + $index}}"  @click.self="$dispatch('modal-close')">
                            @livewire($modal['component'], $modal['data'], $modal['key'])
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endunless
</div>
