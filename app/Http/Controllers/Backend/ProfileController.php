<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pest\Support\View;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('backend.profile.index');
    }
}
