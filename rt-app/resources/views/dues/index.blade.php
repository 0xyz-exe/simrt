@extends('layouts.app')
@section('title', 'Kelola Iuran')
@section('content')
    <div class="mb-6">
        <!-- Back to Dashboard Button -->
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <h1 class="text-2xl font-bold">Kelola Iuran Bulanan</h1>
        <p class="text-slate-600">Generate dan kelola iuran warga RT</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm text-slate-500 mb-1">Total Iuran</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $totalDues }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm text-slate-500 mb-1">Sudah Bayar</h3>
            <p class="text-2xl font-bold text-green-600">{{ $paidDues }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm text-slate-500 mb-1">Belum Bayar</h3>
            <p class="text-2xl font-bold text-red-600">{{ $totalDues - $paidDues }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-sm text-slate-500 mb-1">Total Terkumpul</h3>
            <p class="text-xl font-bold text-purple-600">Rp {{ number_format($paidAmount, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Actions & Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Generate Iuran -->
            <div>
                <h3 class="font-semibold mb-4">Generate Iuran Baru</h3>
                <form method="POST" action="{{ route('dues.generate') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm mb-1">Bulan</label>
                            <select name="month" required class="w-full border rounded px-3 py-2">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Tahun</label>
                            <select name="year" required class="w-full border rounded px-3 py-2">
                                @for($i = date('Y'); $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Jumlah (Rp)</label>
                            <input name="amount" type="number" required value="50000" min="0"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                    </div>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Generate Iuran
                    </button>
                </form>
            </div>

            <!-- Filter -->
            <div>
                <h3 class="font-semibold mb-4">Filter Data</h3>
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm mb-1">Bulan</label>
                            <select name="month" class="w-full border rounded px-3 py-2">
                                <option value="">Semua</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Tahun</label>
                            <select name="year" class="w-full border rounded px-3 py-2">
                                <option value="">Semua</option>
                                @for($i = 2024; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button class="px-4 py-2 bg-slate-600 text-white rounded hover:bg-slate-700">
                        Filter
                    </button>
                    <a href="{{ route('dues.index') }}" class="px-4 py-2 border rounded hover:bg-slate-50">
                        Reset
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Dues Table -->
    @if($dues->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4">Keluarga</th>
                        <th class="text-left py-3 px-4">Periode</th>
                        <th class="text-left py-3 px-4">Jumlah</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dues as $due)
                        <tr class="border-t">
                            <td class="py-3 px-4">
                                <div class="font-semibold">
                                    @if($due->household->kepalaKeluarga)
                                        {{ $due->household->kepalaKeluarga->nama }}
                                    @else
                                        Keluarga {{ $due->household->nomor_kk }}
                                    @endif
                                </div>
                                <div class="text-sm text-slate-500">{{ $due->household->alamat }}</div>
                            </td>
                            <td class="py-3 px-4">
                                {{ DateTime::createFromFormat('!m', $due->month)->format('M') }} {{ $due->year }}
                            </td>
                            <td class="py-3 px-4 font-semibold">
                                Rp {{ number_format($due->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($due->status === 'paid')
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-sm rounded">
                                        Lunas
                                    </span>
                                    @if($due->payments->count() > 0)
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ $due->payments->first()->paid_at->format('d M Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-sm rounded">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($due->status === 'unpaid')
                                    <button onclick="openPaymentModal({{ $due->id }}, '{{ $due->household->kepalaKeluarga?->nama ?? $due->household->nomor_kk }}', {{ $due->amount }})"
                                            class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                        Bayar
                                    </button>
                                @else
                                    <div class="flex gap-2">
                                        <span class="text-sm text-slate-500">Sudah dibayar</span>
                                        @if($due->payments->count() > 0)
                                            <form method="POST" action="{{ route('payments.destroy', $due->payments->first()) }}"
                                                  class="inline" onsubmit="return confirm('Yakin hapus pembayaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline text-sm">Batal</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <p class="text-slate-500 mb-4">Belum ada data iuran untuk periode yang dipilih</p>
            <p class="text-sm text-slate-400">Gunakan form "Generate Iuran Baru" untuk membuat iuran</p>
        </div>
    @endif

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Catat Pembayaran</h3>
            <form method="POST" action="{{ route('payments.store') }}">
                @csrf
                <input type="hidden" name="due_id" id="modal_due_id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1">Keluarga</label>
                        <p id="modal_household_name" class="font-semibold"></p>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Jumlah Bayar (Rp)</label>
                        <input name="amount" type="number" required id="modal_amount"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Metode Pembayaran</label>
                        <select name="method" required class="w-full border rounded px-3 py-2">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Tanggal Bayar</label>
                        <input name="paid_at" type="date" required value="{{ date('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Catatan (opsional)</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border rounded px-3 py-2"
                                  placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Simpan Pembayaran
                    </button>
                    <button type="button" onclick="closePaymentModal()"
                            class="px-4 py-2 border rounded hover:bg-slate-50">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPaymentModal(dueId, householdName, amount) {
            document.getElementById('modal_due_id').value = dueId;
            document.getElementById('modal_household_name').textContent = householdName;
            document.getElementById('modal_amount').value = amount;
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('paymentModal').classList.add('flex');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentModal').classList.remove('flex');
        }
    </script>
@endsection
