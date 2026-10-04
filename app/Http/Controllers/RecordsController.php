<?php

namespace App\Http\Controllers;

class RecordsController extends Controller
{
    public function index()
    {
        return view('records.index');
    }
}