{{-- Point of Sales with Alpine.js --}}
<x-layouts.admin title="Point of Sales">

<div x-data="posApp()" x-init="init()" class="space-y-4">

  {{-- Header --}}
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold">Point of Sales</h1>
        <span class="hidden md:inline text-slate-400">•</span>
        <div class="text-sm text-slate-600">Kasir: <b x-text="state.cashier"></b></div>
        <button x-on:click="showHistoryModal = true; loadHistory()" class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-50">
          📋 History
        </button>
        <button x-on:click="showCoaModal = true" class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-50">
          ⚙️ Setting COA
        </button>
      </div>
      <div class="flex items-center gap-3">
        <div class="text-sm text-slate-600" x-text="nowStr"></div>
        <select x-model="state.outlet" @change="onOutletChange()" class="h-10 rounded-xl border border-slate-200 px-3">
          @foreach($outlets as $outlet)
            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </section>

  <section class="grid grid-cols-1 lg:grid-cols-5 gap-4">
    {{-- Produk (Kiri) --}}
    <div class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
        <div class="md:col-span-2 relative">
          <input type="text" placeholder="Cari SKU/Nama produk… (Enter)" x-model="ui.search" x-on:keydown.enter.prevent="quickAdd()" class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-3" />
          <i class='bx bx-search text-slate-400 absolute left-3 top-1/2 -translate-y-1/2'></i>
        </div>
        <input type="text" placeholder="Scan Barcode (SKU)…" x-model="ui.barcode" x-on:keydown.enter.prevent="scanAdd()" class="h-11 w-full rounded-xl border border-slate-200 px-3" />
      </div>

      <div class="flex flex-wrap gap-2 mb-3">
        <button class="px-3 h-8 rounded-full border text-sm" :class="ui.cat==='all' ? 'bg-primary-100 text-primary-700 border-primary-200' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" x-on:click="ui.cat='all'">Semua</button>
        <template x-for="c in categories" :key="c">
          <button class="px-3 h-8 rounded-full border text-sm" :class="ui.cat===c ? 'bg-primary-100 text-primary-700 border-primary-200' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" x-on:click="ui.cat=c" x-text="c"></button>
        </template>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
        {{-- Loading State --}}
        <template x-if="products.length === 0">
          <div class="col-span-full flex flex-col items-center justify-center py-12 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-primary-600 mb-4"></div>
            <p class="text-lg font-semibold text-slate-700 mb-1">Memuat produk...</p>
            <p class="text-sm text-slate-500" x-text="'Outlet: ' + state.outlet"></p>
            <p class="text-xs text-slate-400 mt-2">Jika loading terlalu lama, cek console (F12)</p>
          </div>
        </template>
        
        {{-- Product Grid --}}
        <template x-for="p in filteredProducts()" :key="p.sku">
          <button class="text-left rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 p-3 shadow-sm flex flex-col" x-on:click="addItem(p)">
            <div class="w-full aspect-square bg-slate-100 rounded-lg mb-2 overflow-hidden flex items-center justify-center">
              <img x-show="p.image" :src="p.image" :alt="p.name" class="w-full h-full object-cover" x-on:error="$event.target.style.display='none'">
              <div x-show="!p.image" class="text-slate-400 text-center p-2">
                <i class='bx bx-image text-4xl'></i>
                <div class="text-xs mt-1">No Image</div>
              </div>
            </div>
            <div class="flex justify-center mb-2 bg-white p-1 rounded">
              <svg class="barcode" :data-code="p.sku" style="max-width: 100%;"></svg>
            </div>
            <div class="font-medium text-sm line-clamp-2" x-text="p.name"></div>
            <div class="text-xs text-slate-500 mt-1" x-text="`SKU: ${p.sku}`"></div>
            <div class="mt-2 text-primary-700 font-bold" x-text="idr(p.price)"></div>
            <div class="text-xs text-slate-500" x-text="p.category"></div>
            <div class="text-xs" :class="p.stock > 0 ? 'text-green-600' : 'text-red-600'" x-text="`Stok: ${p.stock}`"></div>
          </button>
        </template>
      </div>
    </div>

    {{-- Keranjang (Kanan) --}}
    <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-card flex flex-col">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
        <div class="relative">
          <label class="text-xs text-slate-500">Customer</label>
          <input type="text" x-model="ui.customerSearch" x-on:input="searchCustomer()" x-on:focus="ui.customerDropdown=true" placeholder="Cari customer..." class="h-10 w-full rounded-xl border border-slate-200 px-3">
          <div x-show="ui.customerDropdown && filteredCustomers().length > 0" x-on:click.away="ui.customerDropdown=false" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
            <div class="p-2">
              <button x-on:click="selectCustomer(null)" class="w-full text-left px-3 py-2 hover:bg-slate-50 rounded-lg">
                <div class="font-medium">Pelanggan Umum</div>
              </button>
              <template x-for="c in filteredCustomers()" :key="c.id">
                <button x-on:click="selectCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-slate-50 rounded-lg">
                  <div class="flex items-center justify-between">
                    <div class="font-medium" x-text="c.name"></div>
                    <span x-show="c.tipe_name" class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700" x-text="c.tipe_name"></span>
                  </div>
                  <div class="text-xs text-slate-500" x-text="c.telepon"></div>
                  <div class="text-xs" :class="c.piutang > 0 ? 'text-red-600' : 'text-green-600'" x-text="c.piutang > 0 ? 'Piutang: ' + idr(c.piutang) : 'Tidak ada piutang'"></div>
                </button>
              </template>
            </div>
          </div>
          <div x-show="state.customerId" class="mt-1 text-xs text-slate-600">
            <span x-text="selectedCustomer()?.name"></span>
            <span x-show="selectedCustomer()?.tipe_name" class="ml-2 px-2 py-0.5 rounded-full bg-purple-100 text-purple-700" x-text="selectedCustomer()?.tipe_name"></span>
            <span x-show="selectedCustomer()?.piutang > 0" class="text-red-600 ml-2" x-text="'(Piutang: ' + idr(selectedCustomer()?.piutang || 0) + ')'"></span>
          </div>
        </div>
        <div>
          <label class="text-xs text-slate-500">Catatan</label>
          <input x-model="state.note" class="h-10 w-full rounded-xl border border-slate-200 px-3" placeholder="Catatan struk (opsional)" />
        </div>
      </div>

      <div class="grow overflow-y-auto mb-3">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 sticky top-0">
            <tr>
              <th class="px-3 py-2 text-left">Item</th>
              <th class="px-3 py-2 text-center w-24">Qty</th>
              <th class="px-3 py-2 text-right w-28">Harga</th>
              <th class="px-3 py-2 text-right w-28">Subtotal</th>
              <th class="px-3 py-2 w-10"></th>
            </tr>
          </thead>
          <tbody>
            <template x-if="cart.length===0">
              <tr><td colspan="5" class="px-3 py-6 text-center text-slate-500">Belum ada item</td></tr>
            </template>
            <template x-for="(c,i) in cart" :key="c.sku">
              <tr class="border-t">
                <td class="px-3 py-2">
                  <div class="font-medium" x-text="c.name"></div>
                  <div class="text-xs text-slate-500" x-text="c.sku"></div>
                  <div x-show="c.has_discount" class="text-xs text-green-600 mt-1">
                    <span x-text="c.discount_info"></span>
                    <span class="line-through text-slate-400 ml-1" x-text="'(' + idr(c.original_price) + ')'"></span>
                  </div>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center justify-center gap-2">
                    <button class="w-7 h-7 rounded border hover:bg-slate-50" x-on:click="decQty(i)">-</button>
                    <input type="number" min="1" x-model.number="c.qty" x-on:change="recalc()" class="w-12 h-8 rounded border border-slate-200 text-center">
                    <button class="w-7 h-7 rounded border hover:bg-slate-50" x-on:click="incQty(i)">+</button>
                  </div>
                </td>
                <td class="px-3 py-2 text-right" x-text="idr(c.price)"></td>
                <td class="px-3 py-2 text-right" x-text="idr(c.price*c.qty)"></td>
                <td class="px-3 py-2 text-center">
                  <button class="w-7 h-7 rounded border border-rose-200 text-rose-600 hover:bg-rose-50" x-on:click="removeItem(i)">
                    <i class='bx bx-trash'></i>
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div class="border-t pt-3">
        <div class="grid grid-cols-2 gap-2 mb-3">
          <div>
            <label class="text-xs text-slate-500">Diskon</label>
            <div class="flex gap-2">
              <input type="number" min="0" x-model.number="state.discountRp" x-on:change="recalc()" placeholder="Rp" class="h-10 w-full rounded-xl border border-slate-200 px-3">
              <input type="number" min="0" max="100" x-model.number="state.discountPct" x-on:change="recalc()" placeholder="%" class="h-10 w-24 rounded-xl border border-slate-200 px-3">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" class="rounded" x-model="state.tax10" x-on:change="recalc()"> PPN 10%
            </label>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" class="rounded" x-model="state.isBon" x-on:change="recalc()"> Bon (Piutang)
            </label>
          </div>
        </div>

        <div class="text-sm space-y-1 mb-3">
          <div class="flex justify-between"><span>Subtotal</span><b x-text="idr(total.subtotal)"></b></div>
          <div class="flex justify-between"><span>Diskon</span><b x-text="idr(total.discount)"></b></div>
          <div class="flex justify-between" x-show="state.tax10"><span>PPN 10%</span><b x-text="idr(total.tax)"></b></div>
          <div class="flex justify-between text-lg border-t pt-2">
            <span>Total Bayar</span><b x-text="idr(total.grand)"></b>
          </div>
        </div>

        <div class="space-y-2 mb-3" x-show="!state.isBon">
          <div>
            <label class="text-xs text-slate-500 mb-1 block">Metode Pembayaran</label>
            <select x-model="pay.method" class="h-10 w-full rounded-xl border border-slate-200 px-3">
              <option value="cash">💵 Cash</option>
              <option value="transfer">🏦 Transfer</option>
              <option value="qris">📱 QRIS</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-500 mb-1 block">Jumlah Bayar</label>
            <div class="grid grid-cols-3 gap-2">
              <input type="number" min="0" x-model.number="pay.tendered" x-on:input="calcChange()" class="h-10 col-span-2 rounded-xl border border-slate-200 px-3" placeholder="Uang diterima">
              <button x-on:click="pay.tendered = total.grand; calcChange()" class="h-10 rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 font-medium text-sm">
                💰 Lunas
              </button>
            </div>
          </div>
          <div class="flex justify-between text-sm bg-slate-50 p-2 rounded-lg">
            <span>Kembalian</span><b class="text-lg" x-text="idr(pay.change)"></b>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <button class="h-11 rounded-xl border border-slate-200 hover:bg-slate-50" x-on:click="holdOrder()" :disabled="cart.length===0">Tahan</button>
          <button class="h-11 rounded-xl border border-amber-200 hover:bg-amber-50" x-on:click="openHolds()">Ambil Tahanan</button>
          <button class="h-11 rounded-xl border border-rose-200 hover:bg-rose-50" x-on:click="clearCart()" :disabled="cart.length===0">Batal</button>
          <button class="h-11 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50" x-on:click="submitSale()" :disabled="cart.length===0 || (!state.isBon && pay.tendered < total.grand)">
            Bayar & Cetak
          </button>
        </div>
      </div>
    </div>
  </section>

  {{-- Modal Tahanan --}}
  <div x-show="ui.holdOpen" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-xl rounded-2xl bg-white border border-slate-200 shadow-card p-4">
        <div class="flex items-center justify-between mb-3">
          <div class="font-semibold">Order Ditahan</div>
          <button x-on:click="ui.holdOpen=false" class="w-8 h-8 rounded hover:bg-slate-100"><i class='bx bx-x'></i></button>
        </div>
        <div class="max-h-96 overflow-y-auto">
          <template x-if="holds.length===0">
            <div class="p-6 text-center text-slate-500">Belum ada order ditahan.</div>
          </template>
          <template x-for="(h,i) in holds" :key="h.id">
            <div class="border rounded-xl p-3 mb-2">
              <div class="flex items-center justify-between text-sm mb-2">
                <div><b x-text="h.note||'—'"></b><div class="text-xs text-slate-500" x-text="h.time"></div></div>
                <div class="font-semibold" x-text="idr(h.total)"></div>
              </div>
              <div class="flex gap-2">
                <button class="h-9 rounded-lg border border-slate-200 px-3 hover:bg-slate-50" x-on:click="resumeHold(i)">Ambil</button>
                <button class="h-9 rounded-lg border border-rose-200 text-rose-600 px-3 hover:bg-rose-50" x-on:click="removeHold(i)">Hapus</button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Setting COA --}}
  <div x-show="showCoaModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div @click.away="showCoaModal=false" class="w-full max-w-2xl rounded-2xl bg-white border border-slate-200 shadow-lg p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold">Setting COA Point of Sales</h2>
          <button x-on:click="showCoaModal=false" class="w-8 h-8 rounded hover:bg-slate-100">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>
        
        <form x-on:submit.prevent="saveCoaSettings()">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Buku Akuntansi <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.accounting_book_id" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Buku Akuntansi</option>
                <template x-for="book in books" :key="book.id">
                  <option :value="book.id" x-text="book.name"></option>
                </template>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Kas <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_kas" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Kas (Asset)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">💵 Untuk pembayaran tunai (Tipe: Asset)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Bank <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_bank" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Bank (Asset)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">🏦 Untuk pembayaran transfer/QRIS (Tipe: Asset)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Piutang Usaha <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_piutang_usaha" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Piutang (Asset)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📋 Untuk transaksi bon/piutang (Tipe: Asset)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Pendapatan Penjualan <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_pendapatan_penjualan" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Pendapatan (Revenue)</option>
                <template x-for="acc in accountsByType.revenue" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">💰 Pendapatan dari penjualan (Tipe: Revenue)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun PPN (Pajak Pertambahan Nilai)
              </label>
              <select x-model="coaForm.akun_ppn" class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun PPN (Liability - Opsional)</option>
                <template x-for="acc in accountsByType.liability" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📊 Untuk mencatat PPN 10% (Tipe: Liability)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun HPP (Harga Pokok Penjualan)
              </label>
              <select x-model="coaForm.akun_hpp" class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun HPP (Expense - Opsional)</option>
                <template x-for="acc in accountsByType.expense" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📦 Untuk mencatat HPP produk yang terjual (Tipe: Expense)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Persediaan
              </label>
              <select x-model="coaForm.akun_persediaan" class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Persediaan (Asset - Opsional)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📦 Untuk mengurangi nilai persediaan (Tipe: Asset)</p>
            </div>
            
            <div class="flex gap-2 pt-4 border-t">
              <button type="submit" :disabled="coaLoading" class="px-4 h-10 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!coaLoading">💾 Simpan Setting</span>
                <span x-show="coaLoading">⏳ Menyimpan...</span>
              </button>
              <button type="button" x-on:click="showCoaModal=false" class="px-4 h-10 rounded-xl border border-slate-200 hover:bg-slate-50">
                Batal
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal History POS --}}
  <div x-show="showHistoryModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div @click.away="showHistoryModal=false" class="w-full max-w-6xl rounded-2xl bg-white border border-slate-200 shadow-lg max-h-[90vh] overflow-hidden flex flex-col">
        
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b bg-slate-50">
          <h2 class="text-xl font-bold">📋 History Transaksi POS</h2>
          <button x-on:click="showHistoryModal=false" class="w-8 h-8 rounded hover:bg-slate-100">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>

        {{-- Filter --}}
        <div class="p-4 border-b bg-white">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
              <select x-model="historyFilter.status" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
                <option value="all">Semua Status</option>
                <option value="lunas">Lunas</option>
                <option value="menunggu">Menunggu (BON)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Mulai</label>
              <input type="date" x-model="historyFilter.start_date" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Akhir</label>
              <input type="date" x-model="historyFilter.end_date" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Cari</label>
              <input type="text" x-model="historyFilter.search" @input.debounce.500ms="loadHistory()" placeholder="No transaksi..." class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
            </div>
          </div>
        </div>

        {{-- Loading --}}
        <div x-show="historyLoading" class="flex-1 flex items-center justify-center p-8">
          <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-3"></div>
            <p class="text-slate-600">Memuat history...</p>
          </div>
        </div>

        {{-- Table --}}
        <div x-show="!historyLoading" class="flex-1 overflow-auto p-4">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 sticky top-0">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">No Transaksi</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Tanggal</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Customer</th>
                <th class="px-3 py-2 text-right font-semibold text-slate-700">Total</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-700">Pembayaran</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-700">Status</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-700">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <template x-for="item in historyData" :key="item.id">
                <tr class="hover:bg-slate-50">
                  <td class="px-3 py-2">
                    <span class="font-medium text-primary-600" x-text="item.no_transaksi"></span>
                  </td>
                  <td class="px-3 py-2 text-slate-600" x-text="formatDateTime(item.tanggal)"></td>
                  <td class="px-3 py-2">
                    <span x-text="item.member ? item.member.nama : 'Umum'"></span>
                  </td>
                  <td class="px-3 py-2 text-right font-semibold" x-text="formatRupiah(item.total)"></td>
                  <td class="px-3 py-2 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                          :class="{
                            'bg-green-100 text-green-800': item.jenis_pembayaran === 'cash',
                            'bg-blue-100 text-blue-800': item.jenis_pembayaran === 'transfer',
                            'bg-purple-100 text-purple-800': item.jenis_pembayaran === 'qris'
                          }">
                      <span x-show="item.jenis_pembayaran === 'cash'">💵 Cash</span>
                      <span x-show="item.jenis_pembayaran === 'transfer'">🏦 Transfer</span>
                      <span x-show="item.jenis_pembayaran === 'qris'">📱 QRIS</span>
                    </span>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                          :class="{
                            'bg-green-100 text-green-800': item.status === 'lunas',
                            'bg-orange-100 text-orange-800': item.status === 'menunggu'
                          }">
                      <span x-show="item.status === 'lunas'">✅ Lunas</span>
                      <span x-show="item.status === 'menunggu'">⏳ BON</span>
                    </span>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <button @click="printHistoryItem(item.id)" class="inline-flex items-center gap-1 px-2 py-1 rounded bg-primary-50 text-primary-600 hover:bg-primary-100 text-xs">
                      <i class='bx bx-printer'></i> Print
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>

          {{-- Empty State --}}
          <div x-show="historyData.length === 0" class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
              <i class='bx bx-receipt text-3xl text-slate-400'></i>
            </div>
            <p class="text-slate-600 font-medium">Tidak ada transaksi</p>
            <p class="text-sm text-slate-500 mt-1">Belum ada history transaksi untuk filter yang dipilih</p>
          </div>
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t bg-slate-50 flex items-center justify-between">
          <div class="text-sm text-slate-600">
            Total: <b x-text="historyData.length"></b> transaksi
          </div>
          <button x-on:click="showHistoryModal=false" class="px-4 h-9 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-sm">
            Tutup
          </button>
        </div>

      </div>
    </div>
  </div>

  {{-- Modal Print Struk --}}
  <div x-show="showPrintModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-4xl rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b bg-primary-50">
          <h2 class="text-xl font-bold text-primary-900">✅ Transaksi Berhasil - Cetak Struk</h2>
          <button x-on:click="closePrintModal()" class="w-8 h-8 rounded hover:bg-primary-100">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6">
          <!-- Pilihan Jenis Nota -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Jenis Nota:</label>
            <div class="flex gap-3">
              <button x-on:click="updatePreview('besar')" class="flex-1 h-12 rounded-xl border-2 hover:bg-slate-50 transition" :class="printPreviewUrl.includes('type=besar') ? 'border-primary-500 bg-primary-50 text-primary-700 font-semibold' : 'border-slate-200'">
                📄 Nota Besar (A4)
              </button>
              <button x-on:click="updatePreview('kecil')" class="flex-1 h-12 rounded-xl border-2 hover:bg-slate-50 transition" :class="printPreviewUrl.includes('type=kecil') ? 'border-primary-500 bg-primary-50 text-primary-700 font-semibold' : 'border-slate-200'">
                🧾 Nota Kecil (Thermal)
              </button>
            </div>
          </div>

          <!-- Preview -->
          <div class="border-2 border-slate-200 rounded-xl overflow-hidden bg-slate-50" style="height: 500px;">
            <iframe :src="printPreviewUrl" class="w-full h-full" frameborder="0"></iframe>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 p-4 border-t bg-slate-50">
          <button x-on:click="printNota(printPreviewUrl.includes('type=kecil') ? 'kecil' : 'besar')" class="flex-1 h-12 rounded-xl bg-primary-600 text-white hover:bg-primary-700 font-semibold">
            🖨️ Cetak Sekarang
          </button>
          <button x-on:click="closePrintModal()" class="px-6 h-12 rounded-xl border border-slate-200 hover:bg-slate-100">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
function posApp() {
  return {
    state: {
      outlet: {{ $selectedOutlet }},
      cashier: 'Kasir-01',
      customerId: '',
      customerTypeId: null,
      note: '',
      discountRp: 0,
      discountPct: 0,
      tax10: false,
      isBon: false,
    },
    products: [],
    customers: [],
    customerTypePrices: {},
    categories: [],
    cart: [],
    holds: [],
    total: { subtotal: 0, discount: 0, tax: 0, grand: 0 },
    pay: { method: 'cash', tendered: 0, change: 0 },
    ui: { search:'', barcode:'', cat:'all', holdOpen:false, customerSearch:'', customerDropdown:false },
    nowStr: '',
    placeholder: 'data:image/svg+xml;utf8,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512"><rect width="100%" height="100%" fill="#f1f5f9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="Arial" font-size="28">No Image</text></svg>'),
    HOLDS_STORAGE: 'pos.holds',
    showCoaModal: false,
    coaLoading: false,
    books: [],
    accounts: [],
    accountsByType: {
      asset: [],
      liability: [],
      equity: [],
      revenue: [],
      expense: []
    },
    coaForm: {
      accounting_book_id: '',
      akun_kas: '',
      akun_bank: '',
      akun_piutang_usaha: '',
      akun_pendapatan_penjualan: '',
      akun_hpp: '',
      akun_persediaan: '',
      akun_ppn: ''
    },
    showPrintModal: false,
    lastSaleId: null,
    printPreviewUrl: '',
    showHistoryModal: false,
    historyLoading: false,
    historyData: [],
    historyFilter: {
      status: 'all',
      start_date: new Date(new Date().setDate(new Date().getDate() - 7)).toISOString().split('T')[0],
      end_date: new Date().toISOString().split('T')[0],
      search: ''
    },

    async init() {
      await this.loadProducts();
      await this.loadCustomers();
      await this.loadCoaData();
      this.holds = JSON.parse(localStorage.getItem(this.HOLDS_STORAGE)||'[]');
      this.tick();
      setInterval(()=>this.tick(), 1000);
      this.recalc();
    },

    async onOutletChange() {
      console.log('🔄 [POS] Outlet changed to:', this.state.outlet);
      console.time('⏱️ [POS] Outlet change duration');
      
      // Clear products first to show loading state
      console.log('🗑️ [POS] Clearing products and categories...');
      this.products = [];
      this.categories = [];
      
      // Clear cart to avoid mixing products from different outlets
      console.log('🛒 [POS] Clearing cart...');
      this.clearCart();
      
      // Reload products when outlet changes
      console.log('📦 [POS] Loading products for outlet:', this.state.outlet);
      await this.loadProducts();
      
      // Reload COA data for the new outlet
      console.log('💰 [POS] Loading COA data for outlet:', this.state.outlet);
      await this.loadCoaData();
      
      console.timeEnd('⏱️ [POS] Outlet change duration');
      console.log('✅ [POS] Outlet change complete. Products count:', this.products.length);
    },

    async loadProducts() {
      const startTime = performance.now();
      console.log('📦 [POS] loadProducts() started for outlet:', this.state.outlet);
      
      try {
        // Clear products before loading
        this.products = [];
        this.categories = [];
        console.log('🗑️ [POS] Products cleared');
        
        const url = '{{ route("admin.penjualan.pos.products") }}?outlet_id=' + this.state.outlet;
        console.log('🌐 [POS] Fetching from:', url);
        
        // Add timeout warning
        const timeoutWarning = setTimeout(() => {
          console.warn('⚠️ [POS] Request taking longer than 3 seconds...');
        }, 3000);
        
        const fetchStart = performance.now();
        const response = await fetch(url);
        clearTimeout(timeoutWarning);
        
        const fetchDuration = performance.now() - fetchStart;
        console.log(`⏱️ [POS] Fetch completed in ${fetchDuration.toFixed(2)}ms`);
        
        if (fetchDuration > 2000) {
          console.warn(`⚠️ [POS] Slow response detected: ${fetchDuration.toFixed(2)}ms`);
        }
        
        console.log('📡 [POS] Response status:', response.status);
        console.log('📡 [POS] Response headers:', Object.fromEntries(response.headers.entries()));
        
        // Check if response is OK
        if (!response.ok) {
          console.error('❌ [POS] Failed to load products: HTTP ' + response.status);
          alert('Gagal memuat produk. Silakan refresh halaman.');
          return;
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        console.log('📄 [POS] Content-Type:', contentType);
        
        if (!contentType || !contentType.includes('application/json')) {
          console.error('❌ [POS] Response is not JSON:', contentType);
          const text = await response.text();
          console.error('📄 [POS] Response body:', text.substring(0, 500));
          alert('Terjadi kesalahan. Silakan login ulang.');
          return;
        }
        
        const parseStart = performance.now();
        const result = await response.json();
        const parseDuration = performance.now() - parseStart;
        console.log(`⏱️ [POS] JSON parsed in ${parseDuration.toFixed(2)}ms`);
        console.log('📦 [POS] API Response:', result);
        
        if(result.success) {
          const productsData = result.data || [];
          console.log('✅ [POS] Products received:', productsData.length);
          console.log('📦 [POS] Sample product:', productsData[0]);
          
          this.products = productsData;
          this.categories = [...new Set(this.products.map(p=>p.category))];
          console.log('📂 [POS] Categories:', this.categories);
          
          // Generate barcodes after products are loaded
          console.log('🔢 [POS] Scheduling barcode generation...');
          this.$nextTick(() => {
            console.log('🔢 [POS] Generating barcodes...');
            this.generateBarcodes();
            console.log('✅ [POS] Barcodes generated');
          });
          
          const totalDuration = performance.now() - startTime;
          console.log(`✅ [POS] loadProducts() completed in ${totalDuration.toFixed(2)}ms`);
        } else {
          console.error('❌ [POS] Load products failed:', result.message);
          alert('Gagal memuat produk: ' + (result.message || 'Unknown error'));
        }
      } catch(e) {
        const totalDuration = performance.now() - startTime;
        console.error(`❌ [POS] Failed to load products after ${totalDuration.toFixed(2)}ms:`, e);
        console.error('❌ [POS] Error stack:', e.stack);
        alert('Terjadi kesalahan saat memuat produk: ' + e.message);
      }
    },

    async loadCustomers() {
      try {
        const response = await fetch('{{ route("admin.penjualan.pos.customers") }}');
        const result = await response.json();
        if(result.success) {
          this.customers = result.data;
        }
      } catch(e) {
        console.error('Failed to load customers:', e);
      }
    },

    async loadCoaData() {
      const startTime = performance.now();
      console.log('💰 [POS] loadCoaData() started for outlet:', this.state.outlet);
      
      try {
        const headers = {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        };
        
        // Load accounting books
        console.log('📚 [POS] Fetching accounting books...');
        const booksStart = performance.now();
        const booksRes = await fetch('{{ route("finance.accounting-books.data") }}?outlet_id=' + this.state.outlet, { headers });
        const booksDuration = performance.now() - booksStart;
        console.log(`⏱️ [POS] Books fetch: ${booksDuration.toFixed(2)}ms`);
        
        const booksData = await booksRes.json();
        if (booksData.success) {
          this.books = booksData.data || [];
          console.log('✅ [POS] Books loaded:', this.books.length);
        }
        
        // Load chart of accounts
        console.log('📊 [POS] Fetching chart of accounts...');
        const accStart = performance.now();
        const accRes = await fetch('{{ route("finance.chart-of-accounts.data") }}?outlet_id=' + this.state.outlet, { headers });
        const accDuration = performance.now() - accStart;
        console.log(`⏱️ [POS] Accounts fetch: ${accDuration.toFixed(2)}ms`);
        
        const accData = await accRes.json();
        if (accData.success) {
          const allAccounts = accData.data || [];
          this.accounts = allAccounts;
          console.log('✅ [POS] Accounts loaded:', allAccounts.length);
          
          // Filter leaf accounts only (accounts without children)
          const leafAccounts = allAccounts.filter(account => {
            return !allAccounts.some(child => child.parent_code === account.code);
          });
          
          // Group by account type
          this.accountsByType = {
            asset: leafAccounts.filter(a => a.type === 'asset'),
            liability: leafAccounts.filter(a => a.type === 'liability'),
            equity: leafAccounts.filter(a => a.type === 'equity'),
            revenue: leafAccounts.filter(a => a.type === 'revenue'),
            expense: leafAccounts.filter(a => a.type === 'expense')
          };
        }
        
        // Load COA settings
        console.log('⚙️ [POS] Fetching COA settings...');
        const settingsStart = performance.now();
        const settingsRes = await fetch('{{ route("admin.penjualan.pos.coa.settings") }}?outlet_id=' + this.state.outlet, { headers });
        const settingsDuration = performance.now() - settingsStart;
        console.log(`⏱️ [POS] Settings fetch: ${settingsDuration.toFixed(2)}ms`);
        
        const settingsData = await settingsRes.json();
        if (settingsData.success && settingsData.data) {
          this.coaForm = settingsData.data;
          console.log('✅ [POS] COA settings loaded');
        }
        
        const totalDuration = performance.now() - startTime;
        console.log(`✅ [POS] loadCoaData() completed in ${totalDuration.toFixed(2)}ms`);
      } catch(e) {
        const totalDuration = performance.now() - startTime;
        console.error(`❌ [POS] Failed to load COA data after ${totalDuration.toFixed(2)}ms:`, e);
        console.error('❌ [POS] Error stack:', e.stack);
      }
    },

    async saveCoaSettings() {
      this.coaLoading = true;
      try {
        const response = await fetch('{{ route("admin.penjualan.pos.coa.settings.update") }}?outlet_id=' + this.state.outlet, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(this.coaForm)
        });

        const result = await response.json();
        
        if(result.success) {
          alert('✅ Setting COA POS berhasil disimpan');
          this.showCoaModal = false;
        } else {
          alert('❌ Gagal menyimpan: ' + (result.message || 'Unknown error'));
        }
      } catch(e) {
        console.error(e);
        alert('❌ Terjadi kesalahan saat menyimpan');
      } finally {
        this.coaLoading = false;
      }
    },

    async loadHistory() {
      this.historyLoading = true;
      try {
        const params = new URLSearchParams({
          outlet_id: this.state.outlet,
          status: this.historyFilter.status,
          start_date: this.historyFilter.start_date,
          end_date: this.historyFilter.end_date,
          search: this.historyFilter.search
        });

        const response = await fetch(`{{ route('admin.penjualan.pos.history.data') }}?${params}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await response.json();
        
        if (data.success) {
          this.historyData = data.data || [];
        } else {
          console.error('Failed to load history:', data.message);
          this.historyData = [];
        }
      } catch(e) {
        console.error('Error loading history:', e);
        this.historyData = [];
      } finally {
        this.historyLoading = false;
      }
    },

    printHistoryItem(id) {
      const url = `{{ route('admin.penjualan.pos.print', ':id') }}`.replace(':id', id) + '?type=besar';
      window.open(url, '_blank');
    },

    formatDateTime(dateStr) {
      if (!dateStr) return '-';
      const date = new Date(dateStr);
      return date.toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    filteredCustomers() {
      const q = this.ui.customerSearch.trim().toLowerCase();
      if(!q) return this.customers;
      return this.customers.filter(c => 
        c.name.toLowerCase().includes(q) || 
        (c.telepon && c.telepon.includes(q))
      );
    },

    searchCustomer() {
      this.ui.customerDropdown = true;
    },

    async selectCustomer(customer) {
      if(customer) {
        this.state.customerId = customer.id;
        this.state.customerTypeId = customer.id_tipe;
        this.ui.customerSearch = customer.name;
        
        // Load customer type prices if customer has type
        if(customer.id_tipe) {
          await this.loadCustomerTypePrices(customer.id_tipe);
          // Recalculate cart with new prices
          this.applyCustomerTypePrices();
        } else {
          // Reset to normal prices
          this.customerTypePrices = {};
          this.applyCustomerTypePrices();
        }
      } else {
        this.state.customerId = '';
        this.state.customerTypeId = null;
        this.ui.customerSearch = '';
        this.customerTypePrices = {};
        this.applyCustomerTypePrices();
      }
      this.ui.customerDropdown = false;
    },

    async loadCustomerTypePrices(idTipe) {
      try {
        const response = await fetch('{{ route("admin.penjualan.pos.customer-type-prices") }}?id_tipe=' + idTipe + '&outlet_id=' + this.state.outlet);
        const result = await response.json();
        if(result.success) {
          // Convert array to object keyed by id_produk
          this.customerTypePrices = result.data;
        }
      } catch(e) {
        console.error('Failed to load customer type prices:', e);
      }
    },

    applyCustomerTypePrices() {
      // Update cart prices based on customer type
      this.cart = this.cart.map(item => {
        const typePrice = this.customerTypePrices[item.id_produk];
        if(typePrice) {
          return {
            ...item,
            price: typePrice.harga_final,
            original_price: typePrice.harga_normal,
            has_discount: true,
            discount_info: typePrice.harga_khusus > 0 
              ? `Harga Khusus: ${this.idr(typePrice.harga_khusus)}`
              : `Diskon ${typePrice.diskon}%`
          };
        } else {
          // Reset to normal price
          const product = this.products.find(p => p.id_produk === item.id_produk);
          if(product) {
            return {
              ...item,
              price: product.price,
              original_price: product.price,
              has_discount: false,
              discount_info: null
            };
          }
        }
        return item;
      });
      this.recalc();
    },

    selectedCustomer() {
      return this.customers.find(c => c.id == this.state.customerId);
    },

    generateBarcodes() {
      setTimeout(() => {
        document.querySelectorAll('.barcode').forEach(svg => {
          const code = svg.getAttribute('data-code');
          if(code && !svg.innerHTML) {
            try {
              JsBarcode(svg, code, {
                format: 'CODE128',
                width: 1,
                height: 30,
                displayValue: false,
                margin: 0
              });
            } catch(e) {
              console.error('Barcode error:', e);
            }
          }
        });
      }, 100);
    },

    tick() {
      this.nowStr = new Date().toLocaleString('id-ID', {
        weekday:'long', day:'2-digit', month:'long', year:'numeric',
        hour:'2-digit', minute:'2-digit', second:'2-digit'
      });
    },

    filteredProducts() {
      const q = this.ui.search.trim().toLowerCase();
      return this.products.filter(p=>{
        const byCat = this.ui.cat==='all' || p.category===this.ui.cat;
        const byQ   = !q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q);
        const hasStock = p.stock > 0;
        return byCat && byQ && hasStock;
      });
    },

    addItem(p) {
      if (p.stock <= 0) { alert('Stok habis untuk outlet ini.'); return; }
      const ix = this.cart.findIndex(x=>x.sku===p.sku);
      if(ix>=0){
        if (this.cart[ix].qty + 1 > p.stock) { alert('Qty melebihi stok.'); return; }
        this.cart[ix].qty++;
      } else {
        // Check if customer has type discount
        let finalPrice = p.price;
        let hasDiscount = false;
        let discountInfo = null;
        let originalPrice = p.price;
        
        const typePrice = this.customerTypePrices[p.id_produk];
        if(typePrice) {
          finalPrice = typePrice.harga_final;
          originalPrice = typePrice.harga_normal;
          hasDiscount = true;
          discountInfo = typePrice.harga_khusus > 0 
            ? `Harga Khusus: ${this.idr(typePrice.harga_khusus)}`
            : `Diskon ${typePrice.diskon}%`;
        }
        
        this.cart.push({ 
          id_produk: p.id_produk,
          sku: p.sku, 
          name: p.name, 
          price: finalPrice,
          original_price: originalPrice,
          has_discount: hasDiscount,
          discount_info: discountInfo,
          qty: 1,
          satuan: p.satuan,
          tipe: 'produk'
        });
      }
      this.recalc();
    },

    quickAdd() {
      const q = this.ui.search.trim().toLowerCase();
      if(!q) return;
      const p = this.products.find(x=> x.sku.toLowerCase()===q || x.name.toLowerCase().includes(q));
      if(p) this.addItem(p);
      this.ui.search='';
    },

    scanAdd() {
      const s = (this.ui.barcode||'').trim().toLowerCase();
      if(!s) return;
      const p = this.products.find(x=>x.sku.toLowerCase()===s);
      if(p) this.addItem(p);
      this.ui.barcode='';
    },

    incQty(i) {
      const p = this.products.find(x=>x.sku===this.cart[i].sku);
      if (p && this.cart[i].qty + 1 > p.stock) { alert('Qty melebihi stok.'); return; }
      this.cart[i].qty++; this.recalc();
    },

    decQty(i) { 
      if(this.cart[i].qty>1) { 
        this.cart[i].qty--; 
        this.recalc(); 
      } 
    },

    removeItem(i) { 
      this.cart.splice(i,1); 
      this.recalc(); 
    },

    clearCart() {
      if(!confirm('Kosongkan keranjang?')) return;
      this.cart = []; 
      this.state.customerId = '';
      this.state.customerTypeId = null;
      this.ui.customerSearch = '';
      this.customerTypePrices = {};
      this.state.discountRp=0; 
      this.state.discountPct=0; 
      this.state.tax10=false; 
      this.state.isBon=false; 
      this.pay.tendered=0; 
      this.pay.change=0; 
      this.recalc();
    },

    recalc() {
      // Don't override prices if customer type discount is applied
      // Only update price if product doesn't have customer type discount
      this.cart = this.cart.map(c=>{
        if(c.has_discount) {
          // Keep the discounted price
          return c;
        }
        const p = this.products.find(x=>x.sku===c.sku);
        return {...c, price: p ? p.price : c.price};
      });
      const sub = this.cart.reduce((a,b)=> a + b.price*b.qty, 0);
      let disc = Number(this.state.discountRp)||0;
      if(this.state.discountPct>0) disc += sub * (this.state.discountPct/100);
      if(disc>sub) disc=sub;

      let tax = 0;
      const afterDisc = sub - disc;
      if(this.state.tax10) tax = afterDisc*0.10;

      const grand = Math.max(0, Math.round(afterDisc + tax));
      this.total = { subtotal: sub, discount: Math.round(disc), tax: Math.round(tax), grand };
      this.calcChange();
    },

    calcChange() {
      this.pay.change = Math.max(0, (Number(this.pay.tendered)||0) - this.total.grand);
    },

    holdOrder() {
      const h = {
        id: 'H'+Date.now(),
        items: JSON.parse(JSON.stringify(this.cart)),
        note: this.state.note,
        total: this.total.grand,
        time: new Date().toLocaleString('id-ID')
      };
      this.holds.unshift(h);
      localStorage.setItem(this.HOLDS_STORAGE, JSON.stringify(this.holds));
      this.clearCart();
      alert('Order ditahan.');
    },

    openHolds() { 
      this.ui.holdOpen = true; 
    },

    resumeHold(i) {
      const h = this.holds.splice(i,1)[0];
      localStorage.setItem(this.HOLDS_STORAGE, JSON.stringify(this.holds));
      this.cart = h.items; 
      this.state.note = h.note||''; 
      this.recalc();
      this.ui.holdOpen = false;
    },

    removeHold(i) {
      if(!confirm('Hapus order ditahan?')) return;
      this.holds.splice(i,1);
      localStorage.setItem(this.HOLDS_STORAGE, JSON.stringify(this.holds));
    },

    async submitSale() {
      if(this.cart.length === 0) {
        alert('Keranjang masih kosong');
        return;
      }

      if(!this.state.isBon && this.pay.tendered < this.total.grand) {
        alert('Jumlah bayar kurang dari total');
        return;
      }

      const payload = {
        tanggal: new Date().toISOString().slice(0,19).replace('T', ' '),
        id_outlet: this.state.outlet,
        id_member: this.state.customerId || null,
        items: this.cart.map(c => ({
          id_produk: c.id_produk,
          nama_produk: c.name,
          sku: c.sku,
          kuantitas: c.qty,
          satuan: c.satuan,
          harga: c.price,
          subtotal: c.price * c.qty,
          tipe: c.tipe || 'produk'
        })),
        subtotal: this.total.subtotal,
        diskon_nominal: this.state.discountRp,
        diskon_persen: this.state.discountPct,
        ppn: this.total.tax,
        total: this.total.grand,
        jenis_pembayaran: this.pay.method,
        jumlah_bayar: this.state.isBon ? 0 : this.pay.tendered,
        is_bon: this.state.isBon,
        catatan: this.state.note
      };

      try {
        const response = await fetch('{{ route("admin.penjualan.pos.store") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();
        
        if(result.success) {
          // Show print modal
          this.lastSaleId = result.data.id;
          this.printPreviewUrl = '{{ route("admin.penjualan.pos.print", ":id") }}'.replace(':id', result.data.id) + '?type=besar';
          this.showPrintModal = true;
          
          await this.loadProducts();
          this.clearCart();
        } else {
          alert('❌ Gagal menyimpan transaksi: ' + (result.message || 'Unknown error'));
        }
      } catch(e) {
        console.error('Submit error:', e);
        alert('❌ Terjadi kesalahan saat menyimpan transaksi');
      }
    },

    printNota(type) {
      // Add autoprint parameter to trigger print dialog
      const url = '{{ route("admin.penjualan.pos.print", ":id") }}'.replace(':id', this.lastSaleId) + '?type=' + type + '&autoprint=1';
      window.open(url, '_blank');
    },

    updatePreview(type) {
      // Preview without autoprint
      this.printPreviewUrl = '{{ route("admin.penjualan.pos.print", ":id") }}'.replace(':id', this.lastSaleId) + '?type=' + type;
    },

    closePrintModal() {
      this.showPrintModal = false;
      this.lastSaleId = null;
      this.printPreviewUrl = '';
    },

    idr(n) { 
      return (Number(n)||0).toLocaleString('id-ID',{style:'currency',currency:'IDR'}).replace(/\u00A0/g,' '); 
    },
  }
}
</script>

<style>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

</x-layouts.admin>
