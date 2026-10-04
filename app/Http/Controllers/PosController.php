<?php

namespace App\Http\Controllers;

class PosController extends Controller
{
    public function index()
    {
        // Employees only have access to walk-in sales.
        // Redirect them directly to the POS.
        if (auth()->user()->role === 'employee') {
            return redirect()->route('sales.create');
        }

        // Owners get the chooser page.
        return view('pos.index');
    }
}