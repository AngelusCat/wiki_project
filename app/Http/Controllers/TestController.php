<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TestController extends Controller
{
    public function test(): View
    {
        return view('wiki.test.test');
    }
}
