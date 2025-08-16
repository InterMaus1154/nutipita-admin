<div class="space-y-4">
    @if($slot->isNotEmpty())
        <div class="grid grid-cols-3 items-center">
            <div class=""></div>
            <h1 class="justify-self-center text-center r text-lg sm:text-4xl font-bold">{{$title}}</h1>
            <div class="justify-self-end text-right">
                {{$slot}}
            </div>
        </div>
    @else
        <h1 class="justify-self-center text-center r text-lg sm:text-4xl font-bold">{{$title}}</h1>
    @endif
</div>
