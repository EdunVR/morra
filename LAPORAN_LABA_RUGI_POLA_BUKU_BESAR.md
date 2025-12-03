# Implementasi Tabel Laba Rugi dengan Pola Buku Besar

## Struktur yang Harus Diikuti

### 1. HTML Table

```html
<table
    class="w-full text-sm profit-loss-table"
    x-html="renderProfitLossTable()"
></table>
```

### 2. Fungsi renderProfitLossTable()

Fungsi ini akan menghasilkan HTML lengkap untuk tabel (thead + tbody) dengan struktur:

```javascript
renderProfitLossTable() {
  let html = `
    <thead class="bg-slate-50">
      <tr>
        <th class="px-4 py-3 text-left border-r border-slate-200">Kode</th>
        <th class="px-4 py-3 text-left border-r border-slate-200">Nama Akun</th>
        <th class="px-4 py-3 text-right border-r border-slate-200">Jumlah</th>
      </tr>
    </thead>
    <tbody>
  `;

  // PENDAPATAN
  html += `<tr class="bg-slate-50 border-t border-slate-300">
    <td colspan="3" class="px-4 py-3 font-semibold">PENDAPATAN</td>
  </tr>`;

  // Loop revenue accounts
  profitLossData.revenue.accounts.forEach(account => {
    html += `<tr class="border-t border-slate-100 hover:bg-slate-50">
      <td class="px-4 py-2 border-r border-slate-100">
        <span class="font-mono text-xs text-blue-600">${account.code}</span>
      </td>
      <td class="px-4 py-2 border-r border-slate-100">${account.name}</td>
      <td class="px-4 py-2 text-right">
        <span class="text-green-600 font-semibold">${formatCurrency(account.amount)}</span>
      </td>
    </tr>`;
  });

  // Subtotal
  html += `<tr class="border-t-2 border-slate-300 bg-slate-100 font-semibold">
    <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Pendapatan</td>
    <td class="px-4 py-2 text-right">
      <span class="text-green-600">${formatCurrency(profitLossData.revenue.total)}</span>
    </td>
  </tr>`;

  // Spacer
  html += `<tr class="h-4"><td colspan="3" class="bg-slate-50"></td></tr>`;

  // BEBAN (sama seperti pendapatan)

  // GRAND TOTAL
  html += `<tr class="border-t-2 border-slate-400 bg-slate-200 font-bold">
    <td colspan="2" class="px-4 py-3 text-right border-r border-slate-300">LABA/RUGI BERSIH</td>
    <td class="px-4 py-3 text-right">
      <span class="${profitLossData.summary.net_income >= 0 ? 'text-blue-600' : 'text-orange-600'}">
        ${formatCurrency(profitLossData.summary.net_income)}
      </span>
    </td>
  </tr>`;

  html += '</tbody>';
  return html;
}
```

## Keuntungan Pola Ini

1. **Konsisten** - Sama dengan buku besar yang sudah rapih
2. **Sederhana** - Satu fungsi render, tidak ada template Alpine.js yang kompleks
3. **Mudah maintain** - Semua logic di satu tempat
4. **Performant** - Render sekali, tidak ada reactive binding yang kompleks

## Action Items

1. Hapus semua tbody lama dari file index.blade.php
2. Tambahkan fungsi renderProfitLossTable() di JavaScript
3. Test dengan data real
