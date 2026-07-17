<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    // Halaman detail event
    public function show(\App\Models\Event $event)
    {
        // Mengambil daftar kategori untuk keperluan menu header/footer
        $categories = \App\Models\Category::all();

        // Render view detail event dengan data event dan kategori
        return view('event-detail', compact('categories', 'event'));
    }

    // Halaman checkout
    public function checkout(Request $request)
    {
        // Redirect legacy query-based link /checkout?event=13 to the new route /checkout/{event}
        if ($request->has('event')) {
            return redirect()->route('checkout.create', $request->query('event'));
        }

        return view('checkout');
    }
}
