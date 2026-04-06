<div class="space-y-5 p-1">

    {{-- Identitas --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-amber-50 dark:bg-amber-900/20 px-4 py-2 flex items-center gap-2">
            <x-heroicon-o-building-office-2 class="w-4 h-4 text-amber-600" />
            <span class="font-semibold text-sm text-amber-700 dark:text-amber-400">Identitas Vendor</span>
        </div>
        <div class="grid grid-cols-2 gap-x-6 gap-y-3 px-4 py-3 text-sm">
            <div>
                <div class="text-xs text-gray-400 mb-0.5">Nama Vendor</div>
                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $vendor->name }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5">Bentuk Usaha</div>
                <div class="text-gray-700 dark:text-gray-200">{{ $vendor->business_type ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5">PIC / Kontak</div>
                <div class="text-gray-700 dark:text-gray-200">{{ $vendor->pic_name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5">Status</div>
                @if($vendor->is_active)
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 rounded-full px-2 py-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 bg-red-100 rounded-full px-2 py-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Kontak & Alamat --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-2 flex items-center gap-2">
            <x-heroicon-o-map-pin class="w-4 h-4 text-blue-600" />
            <span class="font-semibold text-sm text-blue-700 dark:text-blue-400">Kontak & Alamat</span>
        </div>
        <div class="grid grid-cols-2 gap-x-6 gap-y-3 px-4 py-3 text-sm">
            <div>
                <div class="text-xs text-gray-400 mb-0.5">No HP / Telepon</div>
                <div class="text-gray-700 dark:text-gray-200">{{ $vendor->phone ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5">Email</div>
                <div class="text-gray-700 dark:text-gray-200">{{ $vendor->email ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5">Kota</div>
                <div class="text-gray-700 dark:text-gray-200">{{ $vendor->city ?? '—' }}</div>
            </div>
            <div class="col-span-2">
                <div class="text-xs text-gray-400 mb-0.5">Alamat Lengkap</div>
                <div class="text-gray-700 dark:text-gray-200">{{ $vendor->address ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Rekening Bank --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-green-50 dark:bg-green-900/20 px-4 py-2 flex items-center gap-2">
            <x-heroicon-o-banknotes class="w-4 h-4 text-green-600" />
            <span class="font-semibold text-sm text-green-700 dark:text-green-400">Rekening Bank</span>
        </div>
        @if($vendor->bankAccounts->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($vendor->bankAccounts as $account)
                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
                                    {{ strtoupper(substr($account->bank_name, 0, 3)) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-1.5">
                                    {{ $account->bank_name }}
                                    @if($account->is_primary)
                                        <span class="text-xs font-medium text-amber-600 bg-amber-100 rounded-full px-1.5 py-0.5">Utama</span>
                                    @endif
                                </div>
                                <div class="text-gray-500 text-xs">{{ $account->account_number }} &bull; a/n {{ $account->account_name }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-4 py-4 text-sm text-gray-400 italic text-center">Belum ada rekening bank terdaftar.</div>
        @endif
    </div>

    {{-- Metode Pembayaran --}}
    @if($vendor->payment_methods)
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-purple-50 dark:bg-purple-900/20 px-4 py-2 flex items-center gap-2">
                <x-heroicon-o-credit-card class="w-4 h-4 text-purple-600" />
                <span class="font-semibold text-sm text-purple-700 dark:text-purple-400">Metode Pembayaran</span>
            </div>
            <div class="flex flex-wrap gap-2 px-4 py-3">
                @php
                    $methodLabels = [
                        'transfer' => 'Transfer Bank',
                        'cash'     => 'Tunai (Cash)',
                        'cek'      => 'Cek',
                        'giro'     => 'Giro',
                        'lainnya'  => 'Lainnya',
                    ];
                    $colors = [
                        'transfer' => 'bg-blue-100 text-blue-700',
                        'cash'     => 'bg-green-100 text-green-700',
                        'cek'      => 'bg-yellow-100 text-yellow-700',
                        'giro'     => 'bg-orange-100 text-orange-700',
                        'lainnya'  => 'bg-gray-100 text-gray-600',
                    ];
                    $methods = is_array($vendor->payment_methods) ? $vendor->payment_methods : json_decode($vendor->payment_methods, true) ?? [];
                @endphp
                @foreach($methods as $m)
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $colors[$m] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $methodLabels[$m] ?? $m }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

</div>