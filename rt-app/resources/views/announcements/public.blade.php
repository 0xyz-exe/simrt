@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Pengumuman RT</h1>
        <p class="text-slate-600">Informasi terbaru dari pengurus RT</p>
    </div>

    @if($announcements->count() > 0)
        <div class="space-y-6">
            @foreach($announcements as $announcement)
                <article class="bg-white rounded-lg shadow p-6">
                    <header class="mb-4">
                        <h2 class="text-xl font-semibold">{{ $announcement->title }}</h2>
                        <div class="text-sm text-slate-500 mt-1">
                            {{ $announcement->published_at->format('d M Y, H:i') }}
                        </div>
                    </header>
                    <div class="prose max-w-none">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <p class="text-slate-500">Belum ada pengumuman</p>
        </div>
    @endif
@endsection
