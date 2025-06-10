@if($errors->any())
    <div class="space-y-4 my-4">
        @foreach($errors->all() as $error)
            <p class="text-red-400 text-lg">{{$error}}</p>
        @endforeach
    </div>
@endif
@if(session()->has('error'))
    <div class="space-y-4 my-4">
        <p class="text-red-400 text-lg">{{session()->get('error')}}</p>
    </div>
@endif
