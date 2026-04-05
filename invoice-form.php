<?php $settings = getAgencySettings(); ?>

<div class="max-w-5xl mx-auto">
  <form id="invoiceForm" method="POST" action="print/invoice.php" target="_blank" onsubmit="return prepareInvoiceData()">
    <input type="hidden" name="invoiceData" id="invoiceDataField">

    <!-- Agency Info Display -->
    <div class="bg-base-200 rounded-xl p-4 mb-6 flex items-center gap-4">
      <?php if (!empty($settings['logoPath'])): ?>
        <img src="data/uploads/<?= htmlspecialchars($settings['logoPath']) ?>" alt="Logo" class="w-14 h-14 object-contain rounded">
      <?php endif; ?>
      <div>
        <h3 class="font-bold text-lg"><?= htmlspecialchars($settings['agencyName'] ?? 'Your Agency') ?></h3>
        <p class="text-xs opacity-70"><?= htmlspecialchars($settings['address'] ?? '') ?></p>
        <p class="text-xs opacity-70"><?= htmlspecialchars($settings['email'] ?? '') ?> <?= !empty($settings['phone']) ? '| ' . htmlspecialchars($settings['phone']) : '' ?></p>
      </div>
    </div>

    <!-- Invoice Meta -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div>
        <label class="label"><span class="label-text text-sm font-semibold">Invoice Number</span></label>
        <input type="text" name="invoiceNumber" id="invoiceNumber" class="input input-sm input-bordered w-full" value="INV-<?= date('Ymd') ?>-001">
      </div>
      <div>
        <label class="label"><span class="label-text text-sm font-semibold">Invoice Date</span></label>
        <input type="date" name="invoiceDate" id="invoiceDate" class="input input-sm input-bordered w-full" value="<?= date('Y-m-d') ?>">
      </div>
      <div>
        <label class="label"><span class="label-text text-sm font-semibold">Currency</span></label>
        <select name="currency" id="invoiceCurrency" class="select select-sm select-bordered w-full">
          <option value="OMR">OMR - Omani Rial</option>
          <option value="USD">USD - US Dollar</option>
          <option value="AED">AED - UAE Dirham</option>
          <option value="SAR">SAR - Saudi Riyal</option>
          <option value="QAR">QAR - Qatari Riyal</option>
          <option value="BHD">BHD - Bahraini Dinar</option>
          <option value="KWD">KWD - Kuwaiti Dinar</option>
          <option value="INR">INR - Indian Rupee</option>
          <option value="PKR">PKR - Pakistani Rupee</option>
          <option value="EUR">EUR - Euro</option>
          <option value="GBP">GBP - British Pound</option>
        </select>
      </div>
    </div>

    <!-- Bill To -->
    <div class="bg-base-100 border border-base-300 rounded-xl p-4 mb-6">
      <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Bill To
      </h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="label"><span class="label-text text-sm">Customer Name</span></label>
          <input type="text" name="customerName" id="invCustomerName" class="input input-sm input-bordered w-full" placeholder="Full name" required>
        </div>
        <div>
          <label class="label"><span class="label-text text-sm">Phone</span></label>
          <input type="text" name="customerPhone" id="invCustomerPhone" class="input input-sm input-bordered w-full" placeholder="+968 XXXX XXXX">
        </div>
        <div>
          <label class="label"><span class="label-text text-sm">Address</span></label>
          <input type="text" name="customerAddress" id="invCustomerAddress" class="input input-sm input-bordered w-full" placeholder="Street, City, Country">
        </div>
        <div>
          <label class="label"><span class="label-text text-sm">Email</span></label>
          <input type="email" name="customerEmail" id="invCustomerEmail" class="input input-sm input-bordered w-full" placeholder="email@example.com">
        </div>
      </div>
    </div>

    <!-- Line Items -->
    <div class="bg-base-100 border border-base-300 rounded-xl p-4 mb-6">
      <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Line Items
      </h4>
      <div class="overflow-x-auto">
        <table class="table table-sm w-full" id="invoiceItemsTable">
          <thead>
            <tr class="text-xs">
              <th class="w-1/4">Description</th>
              <th>Passenger Name</th>
              <th>Departure Date</th>
              <th>PNR</th>
              <th class="text-right w-28">Amount</th>
              <th class="w-10"></th>
            </tr>
          </thead>
          <tbody id="invoiceItemsBody">
            <tr class="invoice-item-row">
              <td><input type="text" class="input input-sm input-bordered w-full inv-desc" placeholder="Flight ticket / Service"></td>
              <td><input type="text" class="input input-sm input-bordered w-full inv-pax" placeholder="Passenger name"></td>
              <td><input type="date" class="input input-sm input-bordered w-full inv-dep"></td>
              <td><input type="text" class="input input-sm input-bordered w-full inv-pnr" placeholder="PNR"></td>
              <td><input type="number" step="0.01" min="0" class="input input-sm input-bordered w-full text-right inv-amount" placeholder="0.00" oninput="calcInvoiceTotals()"></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <button type="button" class="btn btn-sm btn-ghost text-primary mt-2" onclick="addInvoiceRow()">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Passenger
      </button>
    </div>

    <!-- Totals -->
    <div class="flex justify-end mb-6">
      <div class="w-full md:w-96 bg-base-100 border border-base-300 rounded-xl p-4 space-y-3">
        <div class="flex justify-between text-sm">
          <span>Subtotal</span>
          <span id="invSubtotal" class="font-semibold">0.00</span>
        </div>
        <div class="flex items-center justify-between gap-2 text-sm">
          <span>Discount</span>
          <div class="flex items-center gap-1">
            <input type="number" step="0.01" min="0" id="invDiscountVal" class="input input-sm input-bordered w-20 text-right" value="0" oninput="calcInvoiceTotals()">
            <div class="btn-group">
              <button type="button" class="btn btn-xs inv-disc-type active" data-type="flat" onclick="setDiscType('flat')">Flat</button>
              <button type="button" class="btn btn-xs inv-disc-type" data-type="percent" onclick="setDiscType('percent')">%</button>
            </div>
            <span class="text-error font-semibold ml-1" id="invDiscountDisplay">-0.00</span>
          </div>
        </div>
        <div class="flex items-center justify-between gap-2 text-sm">
          <span>Tax %</span>
          <div class="flex items-center gap-1">
            <input type="number" step="0.01" min="0" id="invTaxPercent" class="input input-sm input-bordered w-20 text-right" value="0" oninput="calcInvoiceTotals()">
            <span>%</span>
            <span class="font-semibold ml-1" id="invTaxDisplay">+0.00</span>
          </div>
        </div>
        <div class="divider my-1"></div>
        <div class="flex justify-between text-lg font-bold">
          <span>Total</span>
          <span id="invTotal">0.00</span>
        </div>
      </div>
    </div>

    <!-- Notes -->
    <div class="mb-6">
      <label class="label"><span class="label-text text-sm font-semibold">Notes</span></label>
      <textarea id="invNotes" class="textarea textarea-bordered w-full text-sm" rows="2" placeholder="Additional notes or payment instructions..."></textarea>
    </div>

    <!-- Template Selector -->
    <div class="bg-base-100 border border-base-300 rounded-xl p-4 mb-6">
      <h4 class="font-bold text-sm mb-3">Invoice Template</h4>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <label class="cursor-pointer border-2 rounded-lg p-3 text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
          <input type="radio" name="invoiceTemplate" value="modern" class="radio radio-sm radio-primary" checked>
          <div class="mt-2">
            <div class="w-full h-3 rounded" style="background: linear-gradient(90deg, #1e40af, #3b82f6);"></div>
            <span class="text-xs font-semibold mt-1 block">Modern</span>
            <span class="text-[10px] opacity-60">Blue Gradient</span>
          </div>
        </label>
        <label class="cursor-pointer border-2 rounded-lg p-3 text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
          <input type="radio" name="invoiceTemplate" value="classic" class="radio radio-sm radio-primary">
          <div class="mt-2">
            <div class="w-full h-3 rounded flex">
              <div class="w-1/2 h-full rounded-l" style="background: #1e293b;"></div>
              <div class="w-1/2 h-full rounded-r" style="background: #d4a843;"></div>
            </div>
            <span class="text-xs font-semibold mt-1 block">Classic</span>
            <span class="text-[10px] opacity-60">Gold & Navy</span>
          </div>
        </label>
        <label class="cursor-pointer border-2 rounded-lg p-3 text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
          <input type="radio" name="invoiceTemplate" value="minimal" class="radio radio-sm radio-primary">
          <div class="mt-2">
            <div class="w-full h-3 rounded border" style="background: #f9fafb;"></div>
            <span class="text-xs font-semibold mt-1 block">Minimal</span>
            <span class="text-[10px] opacity-60">Clean White</span>
          </div>
        </label>
        <label class="cursor-pointer border-2 rounded-lg p-3 text-center transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
          <input type="radio" name="invoiceTemplate" value="elegant" class="radio radio-sm radio-primary">
          <div class="mt-2">
            <div class="w-full h-3 rounded" style="background: linear-gradient(90deg, #4c1d95, #0d9488);"></div>
            <span class="text-xs font-semibold mt-1 block">Elegant</span>
            <span class="text-[10px] opacity-60">Purple & Teal</span>
          </div>
        </label>
      </div>
    </div>

    <!-- Submit -->
    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        Preview Invoice
      </button>
    </div>
  </form>
</div>

<script>
let discountType = 'flat';

function setDiscType(type) {
  discountType = type;
  document.querySelectorAll('.inv-disc-type').forEach(b => {
    b.classList.toggle('btn-active', b.dataset.type === type);
    b.classList.toggle('active', b.dataset.type === type);
  });
  calcInvoiceTotals();
}

function addInvoiceRow() {
  const tbody = document.getElementById('invoiceItemsBody');
  const row = document.createElement('tr');
  row.className = 'invoice-item-row';
  row.innerHTML = `
    <td><input type="text" class="input input-sm input-bordered w-full inv-desc" placeholder="Flight ticket / Service"></td>
    <td><input type="text" class="input input-sm input-bordered w-full inv-pax" placeholder="Passenger name"></td>
    <td><input type="date" class="input input-sm input-bordered w-full inv-dep"></td>
    <td><input type="text" class="input input-sm input-bordered w-full inv-pnr" placeholder="PNR"></td>
    <td><input type="number" step="0.01" min="0" class="input input-sm input-bordered w-full text-right inv-amount" placeholder="0.00" oninput="calcInvoiceTotals()"></td>
    <td><button type="button" class="btn btn-xs btn-ghost text-error" onclick="removeInvoiceRow(this)">✕</button></td>
  `;
  tbody.appendChild(row);
}

function removeInvoiceRow(btn) {
  btn.closest('tr').remove();
  calcInvoiceTotals();
}

function calcInvoiceTotals() {
  let subtotal = 0;
  document.querySelectorAll('.inv-amount').forEach(inp => {
    subtotal += parseFloat(inp.value) || 0;
  });

  const discVal = parseFloat(document.getElementById('invDiscountVal').value) || 0;
  const taxPct = parseFloat(document.getElementById('invTaxPercent').value) || 0;

  let discount = discountType === 'percent' ? (subtotal * discVal / 100) : discVal;
  if (discount > subtotal) discount = subtotal;

  const afterDiscount = subtotal - discount;
  const tax = afterDiscount * taxPct / 100;
  const total = afterDiscount + tax;

  const cur = document.getElementById('invoiceCurrency').value;
  document.getElementById('invSubtotal').textContent = subtotal.toFixed(2);
  document.getElementById('invDiscountDisplay').textContent = '-' + discount.toFixed(2);
  document.getElementById('invTaxDisplay').textContent = '+' + tax.toFixed(2);
  document.getElementById('invTotal').textContent = cur + ' ' + total.toFixed(2);
}

function prepareInvoiceData() {
  const items = [];
  document.querySelectorAll('.invoice-item-row').forEach(row => {
    items.push({
      description: row.querySelector('.inv-desc').value,
      passenger: row.querySelector('.inv-pax').value,
      departureDate: row.querySelector('.inv-dep').value,
      pnr: row.querySelector('.inv-pnr').value,
      amount: parseFloat(row.querySelector('.inv-amount').value) || 0
    });
  });

  const data = {
    invoiceNumber: document.getElementById('invoiceNumber').value,
    invoiceDate: document.getElementById('invoiceDate').value,
    currency: document.getElementById('invoiceCurrency').value,
    customerName: document.getElementById('invCustomerName').value,
    customerAddress: document.getElementById('invCustomerAddress').value,
    customerPhone: document.getElementById('invCustomerPhone').value,
    customerEmail: document.getElementById('invCustomerEmail').value,
    items: items,
    discountValue: parseFloat(document.getElementById('invDiscountVal').value) || 0,
    discountType: discountType,
    taxPercent: parseFloat(document.getElementById('invTaxPercent').value) || 0,
    notes: document.getElementById('invNotes').value,
    template: document.querySelector('input[name="invoiceTemplate"]:checked').value
  };

  document.getElementById('invoiceDataField').value = JSON.stringify(data);
  return true;
}

// Initial calc
calcInvoiceTotals();
</script>
