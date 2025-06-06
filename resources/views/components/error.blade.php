@if($errors->any())
    <div class="response-holder">
        @foreach($errors->all() as $error)
            <p class="response-text error">{{$error}}</p>
        @endforeach
    </div>
@endif
@if(session()->has('error'))
    <div class="response-holder">
        <p class="response-text error">{{session()->get('error')}}</p>
    </div>
@endif
