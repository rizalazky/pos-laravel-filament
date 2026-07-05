<x-filament-panels::page>
    <div id="pos-wrapper"
        x-data="{ isFullscreen: false }"
        @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
        class="bg-gray-50 dark:bg-gray-900 w-full h-full flex flex-col space-y-4 transition-all duration-300"
        :class="isFullscreen ? 'p-6 md:p-10' : 'p-2 lg:p-4'">
        {{-- 📱 FLOATING BUTTON TRIGGER KERANJANG (HANYA MUNCUL DI MOBILE) --}}
        <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700 flex-none">
    
            {{-- 👤 BAGIAN KIRI: JUDUL POS & INFORMASI OUTLET --}}
            <div class="flex items-center space-x-3">
                {{-- Badge Icon Monitor Kasir --}}
                <div class="p-2.5 bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl hidden sm:block">
                    <x-heroicon-o-computer-desktop class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight uppercase">
                        Point of Sale (POS)
                    </h1>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                        Sistem Kasir Utama • <span class="text-amber-500 dark:text-amber-400 font-bold">{{ auth()->user()->currentOutlet?->name ?? '' }}</span>
                    </p>
                </div>
            </div>

            {{-- 🖥️ BAGIAN KANAN: TOMBOL FULLSCREEN ICON ONLY (CLEAN STYLE) --}}
            <button type="button" 
                    x-data="{ isFullscreen: false }"
                    @click="
                        let target = document.getElementById('pos-wrapper');
                        if (!document.fullscreenElement) {
                            target.requestFullscreen().then(() => isFullscreen = true);
                        } else {
                            document.exitFullscreen().then(() => isFullscreen = false);
                        }
                    "
                    @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
                    class="p-2.5 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300 rounded-xl transition-all duration-200 transform active:scale-95 flex items-center justify-center shadow-sm border border-gray-200 dark:border-gray-700 group focus:outline-none focus:ring-2 focus:ring-amber-500"
                    title="Toggle Layar Penuh">
                
                <template x-if="!isFullscreen">
                    <div class="flex items-center">
                        {{-- Ukuran dikecilkan sedikit ke w-5 h-5 agar proporsinya lebih pas & elegan --}}
                        <x-heroicon-o-arrows-pointing-out class="w-5 h-5 text-gray-400 group-hover:text-amber-500 transition-colors" />
                    </div>
                </template>
                
                <template x-if="isFullscreen">
                    <div class="flex items-center">
                        <x-heroicon-o-arrows-pointing-in class="w-5 h-5 text-rose-500 group-hover:text-rose-600 transition-colors" />
                    </div>
                </template>
            </button>

        </div>


        <div class="fixed bottom-6 right-6 lg:hidden z-40">
            <button type="button" 
                    wire:click="$set('isMobileCartOpen', true)" 
                    class="relative p-4 bg-amber-500 hover:bg-amber-600 text-white rounded-full shadow-2xl transition transform active:scale-95 flex items-center justify-center group">
                <x-heroicon-s-shopping-cart class="w-6 h-6" />  
                
                {{-- Badge jumlah item di keranjang --}}
                @if(count($cart) > 0)
                    <span class="absolute -top-1 -right-1 bg-rose-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                        {{ collect($cart)->sum('qty') }}
                    </span>
                @endif
            </button>
        </div>

        {{-- MAIN GRID LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-13rem)] text-gray-900 dark:text-gray-100 overflow-hidden">
            
            {{-- AREA KIRI: PRODUK (Mengambil 3 kolom penuh di mobile, 2 kolom di desktop) --}}
            <div class="col-span-1 lg:col-span-2 flex flex-col h-full space-y-4 overflow-hidden">
                
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center gap-3 flex-none">
                    <div class="relative flex-1">
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            autofocus
                            placeholder="{{ $searchMode === 'scan' ? 'Scan SKU barcode di sini...' : 'Ketik untuk memfilter katalog...' }}" 
                            class="w-full p-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none dark:text-white text-sm transition font-medium" />
                        
                        <span class="absolute right-3 top-3.5 text-[9px] uppercase font-black tracking-widest px-2 py-0.5 rounded-md shadow-sm border {{ $searchMode === 'scan' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20' }}">
                            {{ $searchMode === 'scan' ? 'Auto-Cart' : 'Filtering' }}
                        </span>
                    </div>
                    <div class="flex bg-gray-100 dark:bg-gray-700/60 p-1 rounded-xl flex-none self-start sm:self-auto">   
                        <button type="button" 
                                wire:click="switchSearchMode('search')"
                                class="px-3 py-1.5 text-xs font-black rounded-lg transition-all flex items-center space-x-1.5 focus:outline-none {{ $searchMode === 'search' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            <span>🔍</span>
                            <span>Cari Manual</span>
                        </button>
                        <button type="button" 
                                wire:click="switchSearchMode('scan')"
                                class="px-3 py-1.5 text-xs font-black rounded-lg transition-all flex items-center space-x-1.5 focus:outline-none {{ $searchMode === 'scan' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            <span>🎯</span>
                            <span>Scan Barcode</span>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        
                        @forelse($this->products as $product)
                            <div wire:click="addToCart({{ $product->id }})"
                                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-amber-500 dark:hover:border-amber-500 cursor-pointer transition active:scale-95 overflow-hidden group">

                                <div class="h-28 bg-gray-100 dark:bg-gray-700">
                                    @if ($product->image)
                                        <img
                                            src="{{ Storage::url($product->image) }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 group-hover:scale-105 transition duration-300">
                                            <x-heroicon-o-cube class="w-10 h-10 text-gray-400"/>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-3 flex flex-col justify-between h-24">
                                    <div>
                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-200 line-clamp-2">
                                            {{ $product->name }}
                                        </h4>
                                    </div>

                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-xs text-gray-400">
                                            Harga
                                        </span>

                                        <span class="font-bold text-amber-600 dark:text-amber-400">
                                            Rp {{ number_format($product->baseUnit->sell_price) }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="col-span-full bg-white dark:bg-gray-800 p-8 text-center text-gray-400 dark:text-gray-500 rounded-xl border border-gray-200 dark:border-gray-700">
                                Belum ada produk terdaftar di database perusahaan Anda...
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

            {{-- AREA KANAN: KERANJANG DESKTOP (🔥 SEKARANG DIKUNCI HANYA UTK LAYAR `lg` KE ATAS) --}}
            <div class="hidden lg:flex bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex-col h-full overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex-none">
                    <h3 class="text-lg font-bold dark:text-white">Keranjang Belanja 🛒</h3>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 space-y-4 flex-none">
                    <label class="text-[10px] font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase tracking-wider">Pelanggan / Member</label>
                    <div class="flex space-x-2">
                        <div class="relative flex-1">
                            @if(!$customerId)
                                <input type="text" 
                                    wire:model.live.debounce.300ms="customerSearch" 
                                    placeholder="Cari nama atau no HP..."
                                    class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none"
                                    autocomplete="off">
                                
                                @if(!empty($customerSearch))
                                    <div class="absolute {{ $isFullscreen ?? false ? 'bottom-full mb-1' : 'top-full mt-1' }} z-50 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto custom-scrollbar">
                                        @if(count($this->customerResults) > 0)
                                            @foreach($this->customerResults as $cust)
                                                <button type="button" 
                                                        wire:click="selectCustomer({{ $cust->id }})"
                                                        class="w-full text-left px-4 py-2 hover:bg-amber-50 dark:hover:bg-amber-900/30 text-sm border-b border-gray-100 dark:border-gray-600 last:border-0 dark:text-white block focus:outline-none">
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $cust->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between mt-0.5">
                                                        <span>📞 {{ $cust->phone_number ?? '-' }}</span>
                                                    </div>
                                                </button>
                                            @endforeach
                                        @else
                                            <div class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                Member tidak ditemukan 😕
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center justify-between p-2 border border-amber-500 rounded-lg bg-amber-50 dark:bg-amber-950/20 text-sm h-[38px] w-full">
                                    <div class="dark:text-white truncate pr-2">
                                        <span class="font-bold text-amber-700 dark:text-amber-400">👤 {{ $selectedCustomerName }}</span> 
                                        <span class="text-xs text-gray-400 dark:text-gray-500">({{ $selectedCustomerPhone }})</span>
                                    </div>
                                    <button type="button" 
                                            wire:click="clearCustomer" 
                                            class="text-rose-500 hover:text-rose-700 text-xs font-bold px-2 transition active:scale-95 flex-none">
                                        Batal
                                    </button>
                                </div>
                            @endif
                        </div>

                        <button type="button" wire:click="openCustomerModal" class="px-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-black text-lg transition active:scale-95 flex items-center justify-center">+</button>
                        <button type="button" wire:click="openRewardModal" class="hidden px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm transition active:scale-95 items-center justify-center" title="Tukar Poin">🎁</button>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    @forelse($cart as $item)
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div class="space-y-1 flex-1 min-w-0 pr-3">
                                <span class="font-bold text-sm block text-gray-800 dark:text-gray-200 truncate">{{ $item['name'] }}</span>
                                
                                @if(!isset($item['is_reward_item']))
                                    <x-filament::modal id="edit-price-desktop-{{ $item['key'] }}" >
                                        <x-slot name="trigger">
                                            <x-filament::icon-button
                                                icon="heroicon-o-pencil-square"
                                                label="edit harga"
                                                size="xs"
                                            />
                                            <span class="text-sm text-gray-400 dark:text-gray-400 block ml-1">Rp {{ number_format($item['price']) }}</span>
                                        </x-slot>
                                        <x-slot name="heading">
                                            Update Harga {{ $item['name'] }}
                                        </x-slot>
                                        <div x-data="{ price: '{{ number_format($item['price']) }}' }">
                                            <x-filament::input.wrapper>
                                                <x-slot name="prefix">
                                                    Rp 
                                                </x-slot>
                                                <x-filament::input
                                                    x-mask:dynamic="$money($input, '.',',')"
                                                    x-model="price"
                                                />         
                                            </x-filament::input.wrapper>
                                            <div class="flex justify-end right mt-3">
                                                <x-filament::button  x-on:click="$wire.updatePrice({{ $item['key'] }}, price, 'desktop')">
                                                    Save
                                                </x-filament::button>
                                            </div>
                                        </div>
                                    </x-filament::modal>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 flex-none">
                                @if(isset($item['is_reward_item']))
                                    <button type="button" wire:click="removeAppliedReward({{ $item['reward_id'] }})" class="font-bold w-7 h-7 py-1.5 rounded-lg shadow bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center transition active:scale-95">
                                        <x-heroicon-o-x-mark class="w-4 h-4 font-bold" />
                                    </button>
                                @endif
                                @if(!isset($item['is_reward_item']))
                                    <button wire:click="updateQty({{ $item['key'] }}, '-')" class="w-7 h-7 bg-white dark:bg-gray-600 shadow-sm border border-gray-200 dark:border-gray-500 text-gray-800 dark:text-gray-200 rounded-lg font-black hover:bg-gray-100 dark:hover:bg-gray-500 flex items-center justify-center transition">
                                         <x-heroicon-o-minus class="w-4 h-4" />
                                    </button>
                                    <span class="font-black text-sm text-center w-6 dark:text-white">{{ $item['qty'] }}</span>
                                    <button wire:click="updateQty({{ $item['key'] }}, '+')" class="w-7 h-7 bg-white dark:bg-gray-600 shadow-sm border border-gray-200 dark:border-gray-500 text-gray-800 dark:text-gray-200 rounded-lg font-black hover:bg-gray-100 dark:hover:bg-gray-500 flex items-center justify-center transition">
                                        <x-heroicon-o-plus class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 dark:text-gray-500 py-12">
                            <span class="text-3xl mb-2">📥</span>
                            <p class="text-xs">Belum ada barang di keranjang</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 space-y-4 flex-none">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase tracking-wider">Potongan Diskon (Rp)</label>
                        <input type="text" x-mask:dynamic="$money($input, '.',',')" wire:model.live="discount" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Total Bayar:</span>
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">Rp {{ number_format($this->total) }}</span>
                    </div>
                    <button wire:click="openPaymentModal" class="w-full py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-black text-md rounded-xl shadow-md transition active:scale-[0.99]">PROSES & CETAK NOTA ⚡</button>
                </div>
            </div>

        </div>

        {{-- 📱 MODAL / DRAWER KERANJANG KHUSUS SCREEN MOBILE --}}
        @if($isMobileCartOpen)
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 lg:hidden animate-fade-in">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-md h-[85vh] overflow-hidden flex flex-col">
                    
                    {{-- Header Mobile Cart --}}
                    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 flex-none">
                        <h4 class="font-black text-lg dark:text-white">Keranjang Belanja (Mobile) 🛒</h4>
                        <button wire:click="$set('isMobileCartOpen', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-xl">✕</button>
                    </div>

                    {{-- Member/Customer Section --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 space-y-4 flex-none">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase tracking-wider">Pelanggan / Member</label>
                            <div class="flex space-x-2">
                                <div class="relative flex-1">
                                    @if(!$customerId)
                                        <input type="text" 
                                            wire:model.live.debounce.300ms="customerSearch" 
                                            placeholder="Cari nama atau no HP..."
                                            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none"
                                            autocomplete="off">
                                        
                                        @if(!empty($customerSearch))
                                            <div class="absolute {{ $isFullscreen ?? false ? 'bottom-full mb-1' : 'top-full mt-1' }} z-50 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto custom-scrollbar">
                                                @if(count($this->customerResults) > 0)
                                                    @foreach($this->customerResults as $cust)
                                                        
                                                        <button type="button" 
                                                                wire:click="selectCustomer({{ $cust->id }})"
                                                                class="w-full text-left px-4 py-2 hover:bg-amber-50 dark:hover:bg-amber-900/30 text-sm border-b border-gray-100 dark:border-gray-600 last:border-0 dark:text-white block focus:outline-none">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $cust->name }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between mt-0.5">
                                                                <span>📞 {{ $cust->phone_number ?? '-' }}</span>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                @else
                                                    <div class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                        Member tidak ditemukan 😕
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center justify-between p-2 border border-amber-500 rounded-lg bg-amber-50 dark:bg-amber-950/20 text-sm h-[38px] w-full">
                                            <div class="dark:text-white truncate pr-2">
                                                <span class="font-bold text-amber-700 dark:text-amber-400">👤 {{ $selectedCustomerName }}</span> 
                                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $selectedCustomerPhone }})</span>
                                            </div>
                                            <button type="button" 
                                                    wire:click="clearCustomer" 
                                                    class="text-rose-500 hover:text-rose-700 text-xs font-bold px-2 transition active:scale-95 flex-none">
                                                Batal
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" wire:click="openCustomerModal" class="px-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-black text-lg transition active:scale-95 flex items-center justify-center">+</button>
                                <button type="button" wire:click="openRewardModal" class="hidden px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm transition active:scale-95 items-center justify-center" title="Tukar Poin">🎁</button>
                            </div>
                        </div>
                    </div>

                    {{-- List Items --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                        @forelse($cart as $item)
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div class="space-y-1 flex-1 min-w-0 pr-3">
                                    <span class="font-bold text-sm block text-gray-800 dark:text-gray-200 truncate">{{ $item['name'] }}</span>
                                    @if(!isset($item['is_reward_item']))
                                        <x-filament::modal id="edit-price-mobile-{{ $item['key'] }}" >
                                            <x-slot name="trigger">
                                                <x-filament::icon-button
                                                    icon="heroicon-o-pencil-square"
                                                    label="edit harga"
                                                    size="xs"
                                                />
                                                <span class="text-sm text-gray-400 dark:text-gray-400 block ml-1">Rp {{ number_format($item['price']) }}</span>
                                            </x-slot>
                                            <x-slot name="heading">
                                                Update Harga {{ $item['name'] }}
                                            </x-slot>
                                            <div x-data="{ price: '{{ number_format($item['price']) }}' }">
                                                <x-filament::input.wrapper>
                                                    <x-slot name="prefix">
                                                        Rp 
                                                    </x-slot>
                                                    <x-filament::input
                                                        x-mask:dynamic="$money($input, '.',',')"
                                                        x-model="price"
                                                    />         
                                                </x-filament::input.wrapper>
                                                <div class="flex justify-end right mt-3">
                                                    <x-filament::button  x-on:click="$wire.updatePrice({{ $item['key'] }}, price, 'mobile')">
                                                        Save
                                                    </x-filament::button>
                                                </div>
                                            </div>
                                        </x-filament::modal>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2 flex-none">
                                    @if(isset($item['is_reward_item']))
                                    <button type="button" wire:click="removeAppliedReward({{ $item['reward_id'] }})" class="font-bold w-7 h-7 py-1.5 rounded-lg shadow bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center transition active:scale-95">
                                        <x-heroicon-o-x-mark class="w-4 h-4 font-bold" />
                                    </button>
                                    @endif
                                    @if(!isset($item['is_reward_item']))
                                        <button wire:click="updateQty({{ $item['key'] }}, '-')" class="w-7 h-7 bg-white dark:bg-gray-600 shadow-sm border border-gray-200 dark:border-gray-500 text-gray-800 dark:text-gray-200 rounded-lg font-black hover:bg-gray-100 dark:hover:bg-gray-500 flex items-center justify-center transition">
                                            <x-heroicon-o-minus class="w-4 h-4" />
                                        </button>
                                        <span class="font-black text-sm text-center w-6 dark:text-white">{{ $item['qty'] }}</span>
                                        <button wire:click="updateQty({{ $item['key'] }}, '+')" class="w-7 h-7 bg-white dark:bg-gray-600 shadow-sm border border-gray-200 dark:border-gray-500 text-gray-800 dark:text-gray-200 rounded-lg font-black hover:bg-gray-100 dark:hover:bg-gray-500 flex items-center justify-center transition">
                                            <x-heroicon-o-plus class="w-4 h-4" />
                                        </button>
                                    @endif    
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 dark:text-gray-500 py-12">
                                <span class="text-3xl mb-2">📥</span>
                                <p class="text-xs">Belum ada barang di keranjang</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer & Action Button --}}
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 space-y-4 flex-none">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase tracking-wider">Potongan Diskon (Rp)</label>
                            <input type="text" x-mask:dynamic="$money($input, '.',',')" wire:model.live="discount" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Total Bayar:</span>
                            <span class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">Rp {{ number_format($this->total) }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="$set('isMobileCartOpen', false)" class="py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-600 dark:text-gray-200 rounded-xl font-bold transition text-sm">Kembali Input</button>
                            {{-- Menutup mobile modal sekalian membuka payment modal utama --}}
                            <button wire:click="openPaymentModal" class="py-3 bg-amber-500 hover:bg-amber-600 text-white font-black text-sm rounded-xl shadow-md transition active:scale-[0.99]">BAYAR SEKARANG ⚡</button>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        {{-- MODAL FORM PEMBAYARAN UTAMA (Tetap Utuh) --}}
        @if($isPaymentModalOpen)
            {{-- ... Isinya sama persis dengan kode lama lu ... --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
                    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                        <h4 class="font-black text-lg dark:text-white">Form Pembayaran 💳</h4>
                        <button wire:click="$set('isPaymentModalOpen', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-xl">✕</button>
                    </div>
                    <div class="p-6 space-y-4 flex-1">
                        <div class="flex justify-between items-center bg-amber-50 dark:bg-amber-950/30 p-3 rounded-xl border border-amber-200 dark:border-amber-900/50">
                            <span class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase">Total Tagihan</span>
                            <span class="text-2xl font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($this->total) }}</span>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase tracking-wider">Metode Pembayaran</label>
                            <select wire:model.live="paymentMethod" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                <option value="cash">💵 Tunai (Cash)</option>
                                @can('multiplePaymentFeature', App\Models\Transaction::class)    
                                    <option value="transfer">🏦 Transfer Bank</option>
                                    <option value="qris">📱 QRIS Digital</option>
                                @endcan
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase tracking-wider">Uang yang Diterima (Rp)</label>
                            <input type="text" x-mask:dynamic="$money($input, '.',',')" wire:model.live="paymentReceived" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white text-lg font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <button wire:click="$set('paymentReceived', {{ $this->total }})" class="p-1.5 bg-gray-100 dark:bg-gray-700 rounded-md text-xs font-semibold hover:bg-gray-200 dark:text-gray-200">💰 Uang Pas</button>
                                <button wire:click="$set('paymentReceived', {{ ceil($this->total / 50000) * 50000 }})" class="p-1.5 bg-gray-100 dark:bg-gray-700 rounded-md text-xs font-semibold hover:bg-gray-200 dark:text-gray-200">💵 Kelipatan 50rb</button>
                            </div>
                        </div>
                        <div class="pt-2 border-t dark:border-gray-700">
                            @if($this->change > 0)
                                <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold">
                                    <span class="text-xs uppercase">Uang Kembalian:</span>
                                    <span class="text-lg font-black">Rp {{ number_format($this->change) }}</span>
                                </div>
                            @elseif($this->underpayment > 0)
                                <div class="flex justify-between items-center text-rose-500 dark:text-rose-400 font-bold bg-rose-50 dark:bg-rose-950/20 p-2.5 rounded-lg border border-rose-200 dark:border-rose-900/40">
                                    <span class="text-xs uppercase">Sisa Kekurangan:</span>
                                    <span class="text-lg font-black">Rp {{ number_format($this->underpayment) }}</span>
                                </div>
                            @else
                                <div class="flex justify-between items-center text-gray-500 dark:text-gray-400 font-bold text-sm">
                                    <span class="uppercase">Status:</span>
                                    <span>Uang Pas (Lunas)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700 grid grid-cols-2 gap-3">
                        <button wire:click="$set('isPaymentModalOpen', false)" class="py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-600 dark:text-gray-200 rounded-xl font-bold transition text-sm">Batal</button>
                        <button wire:click="saveTransaction" class="py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black transition text-sm shadow-md">KONFIRMASI BAYAR</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL REGISTER CUSTOMER BARU (Tetap Utuh) --}}
        @if($isCustomerModalOpen)
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-110 p-4 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
                    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                        <h4 class="font-black text-md dark:text-white">Registrasi Pelanggan Baru 👤</h4>
                        <button wire:click="$set('isCustomerModalOpen', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">✕</button>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="newCustomerName" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Contoh: Budi Santoso" />
                            @error('newCustomerName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 block mb-1 uppercase">Nomor HP / WhatsApp <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="newCustomerPhone" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Contoh: 08123456xxx" />
                            @error('newCustomerPhone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700 grid grid-cols-2 gap-3">
                        <button wire:click="$set('isCustomerModalOpen', false)" class="py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-600 dark:text-gray-200 rounded-lg font-bold transition text-xs">Batal</button>
                        <button wire:click="saveCustomer" class="py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-black transition text-xs shadow-md">SIMPAN MEMBER</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL KATALOG HADIAH POIN (Tetap Utuh) --}}
        @if($isRewardModalOpen)
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-120 p-4 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col h-137.5">
                    <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 flex-none">
                        <div>
                            <h4 class="font-black text-md dark:text-white">Katalog Klaim Hadiah Poin 🎁</h4>
                            <p class="text-[11px] text-gray-400">Pilih hadiah dan pantau antrean klaim secara real-time</p>
                        </div>
                        <button wire:click="$set('isRewardModalOpen', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">✕</button>
                    </div>

                    @php
                        $currentCust = \App\Models\Customer::find($customerId);
                        $pointsUsedRightNow = collect($appliedRewards)->sum(fn($item) => $item['points_required'] * $item['qty']);
                        $realtimePointsLeft = ($currentCust?->points ?? 0) - $pointsUsedRightNow;
                    @endphp
                    <div class="px-4 py-2.5 bg-indigo-50 dark:bg-indigo-950/40 border-b border-indigo-100 dark:border-indigo-900/40 text-xs font-semibold text-indigo-700 dark:text-indigo-400 flex justify-between items-center flex-none">
                        <span>👤 Pelanggan: <span class="font-bold text-gray-900 dark:text-white">{{ $currentCust?->name }}</span></span>
                        <span>🎯 Sisa Saldo Poin: <span class="font-black text-sm text-indigo-600 dark:text-indigo-400">{{ $realtimePointsLeft }} Poin</span></span>
                    </div>
                    <div class="w-full p-4 overflow-y-auto space-y-3 custom-scrollbar h-full">
                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Daftar Pilihan Hadiah</h5>
                        @forelse($this->availableRewards as $reward)

                                @php
                                    $isAlreadyClaimed = isset($appliedRewards[$reward->id]);
                                @endphp
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-between hover:border-indigo-500 transition">
                                <div class="space-y-1 pr-3 min-w-0 flex-1">
                                    <span class="font-bold text-sm block dark:text-white truncate">{{ $reward->reward_name }}</span>
                                    <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-600 rounded text-[10px] text-gray-600 dark:text-gray-300 font-medium">
                                        Tipe: {{ \App\Models\PointReward::getRewardTypes()[$reward->reward_type] ?? $reward->reward_type }}
                                    </span>
                                    @if($reward->reward_type === 'discount')
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium block">Potongan: Rp {{ number_format($reward->discount_value) }}</span>
                                    @endif
                                </div>
                                <div class="text-right flex-none pl-2">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 block mb-1">{{ $reward->points_required }} Poin</span>
                                    @if($isAlreadyClaimed)
                                        <button type="button" wire:click="removeAppliedReward({{ $reward->id }})" class="px-3 py-1.5 text-xs font-black rounded-lg shadow transition bg-rose-600 hover:bg-rose-700 text-white active:scale-95">Batalkan</button>
                                    @elseif($realtimePointsLeft >= $reward->points_required)
                                        <button type="button" wire:click="redeemReward({{ $reward->id }})" class="px-3 py-1.5 text-xs font-black rounded-lg shadow transition bg-indigo-600 hover:bg-indigo-700 text-white active:scale-95">Gunakan</button>
                                    @else
                                        <button type="button" disabled class="px-3 py-1.5 text-xs font-black rounded-lg shadow bg-gray-300 text-gray-500 dark:bg-gray-700 dark:text-gray-400 opacity-60 cursor-not-allowed">Poin tidak memenuhi</button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 py-12">
                                <span class="text-2xl mb-1">🔒</span>
                                <p class="text-xs">Tidak ada katalog hadiah tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700 flex justify-end flex-none">
                        <button wire:click="$set('isRewardModalOpen', false)" class="px-5 py-2 bg-gray-200 dark:bg-gray-600 dark:text-gray-200 rounded-lg font-bold text-xs transition">Selesai & Tutup</button>
                    </div>
                </div>
            </div>
        @endif
        @if($isSuccessModalOpen)
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-130 p-4 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-sm overflow-hidden flex flex-col text-center">
                    
                    {{-- 🎉 HEADER SUKSES --}}
                    <div class="p-6 pb-4 flex flex-col items-center">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center text-3xl mb-3 animate-bounce">
                            ✓
                        </div>
                        <h4 class="text-xl font-black dark:text-white">Transaksi Berhasil!</h4>
                        <p class="text-xs text-gray-400 mt-1">Nota otomatis tersimpan di dalam sistem</p>
                    </div>

                    {{-- 💵 INFORMASI FINANSIAL (KEMBALIAN RAKSASA) --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-y border-gray-100 dark:border-gray-700 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-medium">Uang Diterima:</span>
                            <span class="font-bold dark:text-white">Rp {{ number_format($this->cleanPaymentReceived) }}</span>
                        </div>
                        
                        {{-- Hitung Kembalian Riil --}}
                        <div class="flex justify-between items-center pt-2 border-t border-dashed border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Kembalian:</span>
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format(max(0, $this->cleanPaymentReceived - $this->total)) }}
                            </span>
                        </div>
                    </div>

                    {{-- 🖨️ TOMBOL AKSI UTAMA --}}
                    <div class="p-4 space-y-2 flex flex-col">
                        {{-- Tombol Cetak Struk: Membuka window baru berisi layout struk belanja --}}
                        <form method="POST" action="{{ route('print.receipt') }}" target="_blank">
                            @csrf
                            <input type="hidden" name="sale_id" value="{{ $lastTransactionId }}">
                        
                            <button type="submit" 
                                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black transition text-sm shadow-md flex items-center justify-center space-x-2 active:scale-[0.99]">
                                <x-heroicon-o-printer class="w-4 h-4" />
                                <span>CETAK STRUK BELANJA</span>
                            </button>
                            
                        </form>

                        {{-- Tombol Mulai Ulang --}}
                        <button type="button" 
                                wire:click="startNewTransaction" 
                                class="w-full py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-600 dark:text-gray-200 rounded-xl font-bold transition text-xs">
                            Transaksi Baru (Selesai)
                        </button>
                    </div>

                </div>
            </div>
        @endif
        <script>
            function printStruk(transactionId) {
                if (!transactionId) {
                    alert('ID Transaksi tidak ditemukan!');
                    return;
                }
                
                // Membuka route khusus cetak struk di tab/jendela kecil baru
                window.open('/print/receipt/' + transactionId, '_blank', 'width=400,height=600');
            }
        </script>
    </div>
</x-filament-panels::page>