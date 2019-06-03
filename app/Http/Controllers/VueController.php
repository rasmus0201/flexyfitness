<?php

namespace App\Http\Controllers;

class VueController extends Controller
{
    /**
     * All pages is controlled by frontend
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('app');
    }
}
