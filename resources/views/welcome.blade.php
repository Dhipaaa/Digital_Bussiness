@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12">
    <div class="flex-1 space-y-8">
        <span
            class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">
            #1 Event Platform
        </span>

        <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
            Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
        </h1>

        <p class="text-lg text-slate-500 max-w-lg leading-relaxed">
            Dari konser musik hingga workshop teknologi, semua ada di genggamanmu.
        </p>

        <div class="flex gap-4">
            <a href="#events" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg">
                Mulai Jelajah
            </a>
        </div>
    </div>

    <div class="flex-1">
        <img src="{{ asset('assets/concert.png') }}" class="rounded-[2rem] shadow-2xl w-full object-cover">
    </div>
</section>

<!-- Events -->
<section id="events" class="max-w-7xl mx-auto px-6 py-20">

    <!-- Header -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h2 class="text-3xl font-extrabold mb-2">Event Terdekat</h2>
            <p class="text-slate-500 font-medium">Jangan sampai ketinggalan acara seru minggu ini!</p>
        </div>
    </div>

    <!-- FILTER -->
    <div class="mb-10 flex flex-wrap gap-3 justify-center">

        <a href="/"
            class="px-4 py-2 rounded-xl font-semibold transition
           {{ request('category') ? 'bg-gray-200' : 'bg-indigo-600 text-white' }}">
            Semua Kategori
        </a>

        @foreach ($categories as $cat)
        <a href="/?category={{ $cat->slug }}"
            class="px-4 py-2 rounded-xl font-semibold transition
               {{ request('category') == $cat->slug ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700' }}">
            {{ $cat->name }}
        </a>
        @endforeach

    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- ========================= -->
        <!-- 🔵 EVENT STATIS (JANGAN DIHAPUS) -->
        <!-- ========================= -->

        <!-- Card 1 -->
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl overflow-hidden">
            <div class="relative aspect-[3/4]">
                <img src="{{ asset('assets/concert.png') }}" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 bg-white px-3 py-1 text-xs font-bold text-indigo-600 rounded">
                    Musik
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">Jazz Night 2024</h3>
                <p class="text-sm text-gray-500 mb-3">16 November 2024</p>
                <div class="flex justify-between">
                    <span class="text-xl font-bold text-indigo-600">Rp 150rb</span>
                    <a href="#" class="text-indigo-600">Detail</a>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl overflow-hidden">
            <div class="relative aspect-[3/4]">
                <img src="{{ asset('assets/workshop.png') }}" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 bg-white px-3 py-1 text-xs font-bold text-indigo-600 rounded">
                    Technology
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">AI & Future</h3>
                <p class="text-sm text-gray-500 mb-3">26 October 2024</p>
                <div class="flex justify-between">
                    <span class="text-xl font-bold text-indigo-600">Rp 50rb</span>
                    <a href="#" class="text-indigo-600">Detail</a>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl overflow-hidden">
            <div class="relative aspect-[3/4]">
                <img src="{{ asset('assets/hackathon.png') }}" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 bg-white px-3 py-1 text-xs font-bold text-indigo-600 rounded">
                    Coding
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">Hackathon 2024</h3>
                <p class="text-sm text-gray-500 mb-3">18-20 October 2024</p>
                <div class="flex justify-between">
                    <span class="text-xl font-bold text-indigo-600">Gratis</span>
                    <a href="#" class="text-indigo-600">Detail</a>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!-- 🟢 EVENT DINAMIS -->
        <!-- ========================= -->

        @forelse($events as $event)
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl overflow-hidden">

            <div class="relative aspect-[3/4]">
                @php
                $posterUrl = ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                ? asset('storage/' . $event->poster_path)
                : 'https://placehold.co/200x600';
                @endphp

                <img src="{{ $posterUrl }}" alt="{{ $event->title }}" class="w-full h-full object-cover">

                <div class="absolute top-4 left-4 bg-white px-3 py-1 text-xs font-bold text-indigo-600 rounded">
                    {{ $event->category->name ?? 'Tanpa Kategori' }}
                </div>
            </div>

            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">
                    {{ $event->title }}
                </h3>

                <p class="text-sm text-gray-500 mb-3">
                    {{ \Carbon\Carbon::parse($event->date)->format('d M Y H:i') }}
                </p>

                <div class="flex justify-between">
                    <span class="text-xl font-bold text-indigo-600">
                        Rp {{ number_format($event->price, 0, ',', '.') }}
                    </span>

                    <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600">
                        Detail
                    </a>
                </div>
            </div>

        </div>
        @empty
        <p class="col-span-3 text-center text-gray-500">
            Tidak ada event
        </p>
        @endforelse

    </div>

</section>

<!-- Partners Section -->
<section class="max-w-7xl mx-auto px-6 py-20 bg-gradient-to-b from-slate-50 to-white rounded-3xl">
    <!-- Header -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-slate-900 mb-2">Partner Kami</h2>
        <p class="text-slate-600 text-lg">Dukungan dari berbagai organisasi dan perusahaan terkemuka</p>
    </div>

    <!-- Partners Grid -->
    @if ($partners->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 items-center">
        @foreach ($partners as $partner)
        <div
            class="bg-white rounded-2xl border border-slate-100 p-6 hover:shadow-lg hover:border-indigo-200 transition">
            <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="w-full h-20 object-contain">
            <p class="text-center text-sm text-slate-600 mt-4 font-medium">{{ $partner->name }}</p>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center text-slate-500 py-12">
        <p>Partner akan ditampilkan di sini</p>
    </div>
    @endif
</section>

@endsection