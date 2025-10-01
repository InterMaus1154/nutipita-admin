<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class AppearanceUpdateForm extends Component
{

    public int $colorMode;
    public int $themeMode;
    public int $fontSize;

    public function mount(): void
    {
        $settings = auth()->user()?->settings;
        $fontSize = $settings?->user_font_size->value ?? 16;
        $colorMode = $settings?->user_color_mode->value ?? 0;
        $themeMode = $settings?->user_theme_mode->value ?? 0;

        $this->colorMode = $colorMode;
        $this->themeMode = $themeMode;
        $this->fontSize = $fontSize;
    }

    public function updated(): void
    {
        DB::beginTransaction();
        try{
            auth()->user()->settings()->update([
                'user_font_size' => $this->fontSize,
                'user_color_mode' => $this->colorMode,
                'user_theme_mode' => $this->themeMode
            ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('Error updating user settings');
            Log::error($e->getMessage());
            session()->flash('error', 'Settings couldnt be updated');
        }

        $this->js('window.location.reload()');
    }

    public function render(): View
    {
        return view('livewire.setting.appearance-update-form');
    }
}
