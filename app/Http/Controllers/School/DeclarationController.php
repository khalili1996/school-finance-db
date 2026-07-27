<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{
    public function index()
    {
        return view('school.declarations.index');
    }
}
