<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class UserSettingController extends Controller
{
    public function appearancePage(): View
    {
        return view('settings.appearance');
    }
}
