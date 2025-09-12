@props([
    'placeholder' => '',
    'wireModel' => null,
    'name' => '',
    'wireChange' => null,
    'width' => 'w-[250px]',
    'bg' => '' /*default background*/
])
<div
    class="cursor-pointer  relative min-w-[150px] {{$width}}"
    x-data="{
        open: false,
        selected: @if($wireModel) @entangle($wireModel).live @else '' @endif,
        selectedText: @js($placeholder),
        setText(){
            if (this.selected === '' || this.selected === null) {
                this.selectedText = @js($placeholder);
            } else {
                let el = $el.querySelector(`[data-value='${this.selected}']`);
                this.selectedText = el ? el.innerText.trim() : @js($placeholder);
            }
        }
    }"
    x-effect="setText();"
    x-init="setText();
        @if($wireChange)
         $watch('selected', (newValue)=>{
            $wire.call('{{$wireChange}}', newValue);
         });
         @endif
    "
>
    {{--selected value--}}
    <div class="border-2 border-[#666] bg-[#393939] rounded-2xl flex justify-center items-center cursor-pointer text-center min-h-[40px] transition-all {{$bg}}"
         x-on:click="open = !open"
         x-text="selectedText"
         x-bind:class="{
            'text-accent': selectedText !== @js($placeholder),
            'dark:text-white': selectedText === @js($placeholder),
            'custom-select-glowing border-transparent': open
         }"
    >

    </div>

    {{--input for regular form submission--}}
    @if($name)
        <input type="hidden" name="{{$name}}" x-bind:value="selected">
    @endif

    {{--options list--}}
    <ul x-show="open"
        x-cloak
        x-on:click.outside="open = false"
        class="border-1 border-black/60 shadow-md shadow-black absolute top-[120%] text-center left-0 right-0 h-auto max-h-[240px] overflow-y-scroll flex flex-col gap-1 py-2 px-2 bg-[#333] rounded-2xl origin-top z-10"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-x-50 scale-y-10"
        x-transition:enter-end="opacity-100 scale-x-100 scale-y-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-x-100 scale-y-100"
        x-transition:leave-end="opacity-0 scale-x-40 scale-y-0"
    >
        {{$options}}
    </ul>


</div>
