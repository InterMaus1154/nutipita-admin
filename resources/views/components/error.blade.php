@if($errors->any())
    <div class="space-y-4 my-4">
        @foreach($errors->all() as $error)
            <div class="flex gap-1 items-center">
                <flux:icon.exclamation-triangle class="text-red-400 text-lg"/>
                <p class="text-red-400 text-lg">{{$error}}</p>
            </div>
        @endforeach
    </div>
@endif
@if(session()->has('error'))
    <div class="space-y-4 my-4">
        <div class="flex gap-1 items-center">
            <flux:icon.exclamation-triangle class="text-red-400 text-lg"/>
            <p class="text-red-400 text-lg">{{session()->get('error')}}</p>
        </div>
    </div>
@endif
