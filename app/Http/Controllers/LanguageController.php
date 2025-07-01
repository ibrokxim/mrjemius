<?php

namespace App\Http\Controllers;


class LanguageController extends Controller
{
    public function switchLanguage($language)
    {
        if (in_array($language, ['uz','ru'])) {
            session()->put('locale', $language);
            session()->save();
        }
        return redirect()->back();
    }

}
