@if(session()->has('success'))
    <div class="response-holder">
        <p class="response-text success">{{session()->get('success')}}</p>
    </div>
@endif
