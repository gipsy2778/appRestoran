<?php

namespace App\Http\Controllers;

class KasirController extends Controller
{
    public function dashboard()
    {
        return view('kasir.dashboard');
    }   

    public function menuIndex()
    {
        $menu = \App\Models\Menu::with('resepDetail.bahanBaku')->get();
        return view('kasir.menu', compact('menu'));
    }
}