@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Manajemen Event</h2>

        <a href="{{ route('admin.events.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded font-semibold hover:bg-indigo-700">
            Tambah Event
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-4 rounded mb-5 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-lg shadow-sm border border-gray-200 text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="p-4 font-semibold text-gray-600">Judul Event</th>
                    <th class="p-4 font-semibold text-gray-600">Poster</th>
                    <th class="p-4 font-semibold text-gray-600">Kategori</th>
                    <th class="p-4 font-semibold text-gray-600">Tanggal</th>
                    <th class="p-4 font-semibold text-gray-600">Aksi Pilihan</th>
                </tr>
            </thead>

            <tbody>
                @foreach($events as $event)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="p-4 text-gray-800">
                        {{ $event->title }}
                    </td>

                    <td class="p-4">
                        @php
                        $posterUrl = ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                        ? asset('storage/' . $event->poster_path)
                        : 'https://placehold.co/96x64';
                        @endphp

                        <img src="{{ $posterUrl }}" alt="Poster" class="w-24 h-16 object-cover rounded">
                    </td>

                    <td class="p-4 text-indigo-600">
                        {{ $event->category->name ?? '-' }}
                    </td>

                    <td class="p-4 text-gray-600">
                        {{ $event->date->format('d M Y, H:i') }}
                    </td>

                    <td class="p-4 flex gap-2">

                        {{-- EDIT (sesuai instruksi modul) --}}
                        <a href="{{ route('admin.events.edit', $event->id) }}"
                            class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded text-sm font-semibold hover:bg-blue-600 hover:text-white transition">
                            Edit Data
                        </a>

                        {{-- DELETE --}}
                        <form action="{{ route('admin.events.destroy', $event->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus event ini?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded text-sm font-semibold hover:bg-red-600 hover:text-white transition">
                                Hapus
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach

                @if($events->isEmpty())
                <tr>
                    <td colspan="4" class="text-center p-4 text-gray-500">
                        Belum ada data event.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection