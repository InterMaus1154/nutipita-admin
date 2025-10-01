<?php

namespace App\Models;

use App\Enums\settings\UserColorMode;
use App\Enums\settings\UserFontSize;
use App\Enums\settings\UserThemeMode;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $table = 'user_settings';
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected function casts(): array
    {
        return [
            'user_color_mode' => UserColorMode::class,
            'user_theme_mode' => UserThemeMode::class,
            'user_font_size' => UserFontSize::class
        ];
    }

}
