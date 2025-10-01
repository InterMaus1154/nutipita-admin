@use(App\Enums\settings\UserColorMode)
@use(App\Enums\settings\UserThemeMode)
@use(App\Enums\settings\UserFontSize)
<div>
    <div>
        <x-error />
        <x-success />
    </div>
    <form class="flex flex-col gap-4 items-center">
        @csrf
        <x-form.form-wrapper>
            <x-form.form-label text="Color Mode"/>
            <x-ui.select.select :has-wire="true" wire-model="colorMode">
                <x-slot:options>
                    @foreach(UserColorMode::cases() as $colorMode)
                        <x-ui.select.option value="{{$colorMode->value}}" text="{{ucfirst(strtolower($colorMode->name))}}"/>
                    @endforeach
                </x-slot:options>
            </x-ui.select.select>
        </x-form.form-wrapper>
        <x-form.form-wrapper>
            <x-form.form-label text="Theme"/>
            <x-ui.select.select :has-wire="true" wire-model="themeMode">
                <x-slot:options>
                    @foreach(UserThemeMode::cases() as $themeMode)
                        <x-ui.select.option value="{{$themeMode->value}}" text="{{ucfirst(strtolower($themeMode->name))}}"/>
                    @endforeach
                </x-slot:options>
            </x-ui.select.select>
        </x-form.form-wrapper>
        <x-form.form-wrapper>
            <x-form.form-label text="Font Size"/>
            <x-ui.select.select :has-wire="true" wire-model="fontSize">
                <x-slot:options>
                    @foreach(UserFontSize::cases() as $fontSize)
                        <x-ui.select.option value="{{$fontSize->value}}" text="{{ucfirst(strtolower(substr($fontSize->name, 1)))}}"/>
                    @endforeach
                </x-slot:options>
            </x-ui.select.select>
        </x-form.form-wrapper>
    </form>
</div>
