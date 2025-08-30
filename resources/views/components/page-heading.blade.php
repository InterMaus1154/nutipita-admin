<div class="space-y-4">
    @if($slot->isNotEmpty())
        <div class="grid grid-cols-[1fr_auto_1fr] items-center">
            <div></div>
            <h1 class="justify-self-center text-right text-lg sm:text-4xl font-bold">{{$title}}</h1>
            <div class="justify-self-end text-right">
                {{$slot}}
            </div>
        </div>
    @else
        <h1 class="justify-self-center text-center r text-lg sm:text-4xl font-bold">{{$title}}</h1>
    @endif
</div>
