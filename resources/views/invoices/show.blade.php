<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Invoice: <span class="font-mono">{{ $invoice->invoice_number }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Invoice</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Nomor Invoice:</p>
                                <p class="font-semibold">{{ $invoice->invoice_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Invoice:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Jatuh Tempo:</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status Pembayaran:</p>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                    @if($invoice->payment_status === 'Paid') bg-green-100 text-green-800
                                    @elseif($invoice->payment_status === 'Partial') bg-amber-100 text-amber-800
                                    @elseif($invoice->payment_status === 'Unpaid') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $invoice->payment_status_display }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Order</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Customer:</p>
                                <p class="font-semibold">{{ $invoice->order->customer->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Produk:</p>
                                <p class="font-semibold">{{ $invoice->order->product_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Jumlah:</p>
                                <p class="font-semibold">{{ $invoice->order->quantity }} pcs</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Harga per Unit:</p>
                                @if($invoice->order->product_type === 'custom')
                                    @php
                                        $totalPembelian = $invoice->order->purchases->sum(function ($purchase) {
                                            return $purchase->quantity * $purchase->price;
                                        });
                                        $totalBiayaProduksi = $invoice->order->productionCosts->sum('amount');
                                        $totalHPP = $totalPembelian + $totalBiayaProduksi;
                                    @endphp
                                    @if($totalHPP > 0)
                                        <p class="font-semibold text-blue-600">HPP: Rp {{ number_format($totalHPP, 0, ',', '.') }}</p>
                                        <p class="text-sm text-gray-500">+ Margin (akan ditentukan)</p>
                                    @else
                                        <p class="font-semibold text-gray-500">Akan dihitung setelah produksi selesai</p>
                                    @endif
                                @else
                                    <p class="font-semibold">Rp {{ number_format($invoice->order->total_price ?? 0, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-6">

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rincian Biaya</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @if($invoice->order->product_type === 'custom')
                            @php
                                $totalPembelian = $invoice->order->purchases->sum(function ($purchase) {
                                    return $purchase->quantity * $purchase->price;
                                });
                                $totalBiayaProduksi = $invoice->order->productionCosts->sum('amount');
                                $totalHPP = $totalPembelian + $totalBiayaProduksi;
                            @endphp
                            @if($totalHPP > 0)
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-700">Total Pembelian Material:</span>
                                        <span class="font-semibold text-blue-600">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-700">Total Biaya Produksi:</span>
                                        <span class="font-semibold text-blue-600">Rp {{ number_format($totalBiayaProduksi, 0, ',', '.') }}</span>
                                    </div>
                                    <hr class="border-gray-300 my-2">
                                    <div class="flex justify-between items-center py-2 text-lg">
                                        <span class="font-semibold text-blue-900">Total HPP:</span>
                                        <span class="font-bold text-blue-900">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-blue-100 p-3 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <strong>Info:</strong> Harga jual akan dihitung dari HPP + margin yang akan ditentukan setelah produksi selesai.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-600">Harga akan dihitung setelah produksi selesai</p>
                                    <p class="text-sm text-gray-500 mt-2">Berdasarkan HPP + margin</p>
                                </div>
                            @endif
                        @else
                            <div class="flex justify-between items-center py-2">
                                <span>Subtotal:</span>
                                <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span>Pajak:</span>
                                <span class="font-semibold">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between items-center py-2 text-lg">
                                <span class="font-semibold">Total:</span>
                                <span class="font-bold text-green-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Pembayaran -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        @if($invoice->order->incomes->count() > 0)
                            <div class="space-y-3">
                                @foreach($invoice->order->incomes as $income)
                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-blue-900">{{ $income->type }}</span>
                                            <span class="text-sm text-blue-600 ml-2">({{ \Carbon\Carbon::parse($income->date)->format('d M Y') }})</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-semibold text-blue-900">Rp {{ number_format($income->amount, 0, ',', '.') }}</span>
                                            @if($income->payment_method)
                                                <div class="text-xs text-blue-600">{{ $income->payment_method_display }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                <hr class="border-blue-200 my-3">
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-semibold text-blue-900">Total Dibayar:</span>
                                    <span class="font-bold text-blue-900">Rp {{ number_format($invoice->order->incomes->sum('amount'), 0, ',', '.') }}</span>
                                </div>
                                @php
                                    $totalOrderValue = $invoice->order->product_type === 'custom' ? 
                                        ($invoice->subtotal > 0 ? $invoice->subtotal : 0) : 
                                        ($invoice->order->total_price * $invoice->order->quantity);
                                    $totalPaid = $invoice->order->incomes->sum('amount');
                                    $remainingAmount = $totalOrderValue - $totalPaid;
                                @endphp
                                @if($remainingAmount > 0)
                                    <div class="flex justify-between items-center py-2">
                                        <span class="font-semibold text-red-600">Sisa Pembayaran:</span>
                                        <span class="font-bold text-red-600">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                                    </div>
                                @elseif($invoice->order->product_type === 'custom' && $totalPaid > 0)
                                    <div class="flex justify-between items-center py-2">
                                        <span class="font-semibold text-green-600">Status:</span>
                                        <span class="font-bold text-green-600">DP Dibayar (Rp {{ number_format($totalPaid, 0, ',', '.') }})</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-blue-600">Belum ada pembayaran yang diterima</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    @if($invoice->status !== 'Paid')
                        <form action="{{ route('invoices.updateStatus', $invoice) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Sent">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Kirim Invoice
                            </button>
                        </form>

                        <form action="{{ route('invoices.updateStatus', $invoice) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Paid">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Tandai Lunas
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('invoices.download', $invoice) }}" 
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Download PDF
                    </a>

                    <a href="{{ route('orders.show', $invoice->order) }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali ke Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 