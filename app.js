// ===== TAB SWITCHING =====
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('active');
    });
    // Deactivate all tabs
    document.querySelectorAll('[role="tab"]').forEach(el => {
        el.classList.remove('tab-active');
    });
    // Show selected tab
    const tabContent = document.getElementById('tab-' + tabName);
    if (tabContent) {
        tabContent.classList.remove('hidden');
        tabContent.classList.add('active');
    }
    // Activate tab button
    const tabBtn = document.querySelector('[data-tab="' + tabName + '"]');
    if (tabBtn) tabBtn.classList.add('tab-active');
}

// Initialize first tab
document.addEventListener('DOMContentLoaded', () => {
    switchTab('booking');
    loadAgencySettingsToForm();
});

// ===== TOAST NOTIFICATIONS =====
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ===== AGENCY SETTINGS =====
function loadAgencySettingsToForm() {
    // Pre-fill contact fields in booking form from agency settings
    fetch('api/settings.php?action=get')
        .then(r => r.json())
        .then(settings => {
            // Pre-fill booking contact fields
            const emailInputs = document.querySelectorAll('.passenger-email');
            const phoneInputs = document.querySelectorAll('.passenger-phone');
            emailInputs.forEach(el => { if (!el.value) el.value = settings.email || ''; });
            phoneInputs.forEach(el => { if (!el.value) el.value = settings.phone || ''; });
        })
        .catch(() => {});
}

function saveAgencySettings(event) {
    event.preventDefault();
    const form = document.getElementById('agency-settings-form');
    const formData = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    const origText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner"></span> Saving...';
    btn.disabled = true;

    fetch('api/settings.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Settings saved successfully!');
            // Update image previews
            if (data.settings.logoPath) {
                const el = document.getElementById('logo-preview');
                if (el) { el.src = 'data/uploads/' + data.settings.logoPath + '?t=' + Date.now(); el.classList.remove('hidden'); }
            }
            if (data.settings.stampPath) {
                const el = document.getElementById('stamp-preview');
                if (el) { el.src = 'data/uploads/' + data.settings.stampPath + '?t=' + Date.now(); el.classList.remove('hidden'); }
            }
            if (data.settings.signaturePath) {
                const el = document.getElementById('signature-preview');
                if (el) { el.src = 'data/uploads/' + data.settings.signaturePath + '?t=' + Date.now(); el.classList.remove('hidden'); }
            }
        } else {
            showToast('Failed to save settings', 'error');
        }
    })
    .catch(() => showToast('Error saving settings', 'error'))
    .finally(() => { btn.innerHTML = origText; btn.disabled = false; });
}

// ===== PASSENGER MANAGEMENT (Booking) =====
let passengerCount = 1;

function addPassenger() {
    passengerCount++;
    const container = document.getElementById('passengers-container');
    const card = document.createElement('div');
    card.className = 'passenger-card';
    card.id = 'passenger-' + passengerCount;
    card.innerHTML = `
        <button type="button" class="btn btn-ghost btn-xs remove-btn text-error" onclick="removePassenger(${passengerCount})">✕</button>
        <div class="font-bold text-sm mb-2 text-primary">Passenger ${passengerCount}</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="label"><span class="label-text text-sm">Passenger Name *</span></label>
                <input type="text" class="input input-sm input-bordered w-full passenger-name" placeholder="Full Name" required>
            </div>
            <div>
                <label class="label"><span class="label-text text-sm">Ticket Number</span></label>
                <input type="text" class="input input-sm input-bordered w-full passenger-ticket" placeholder="Ticket No.">
            </div>
            <div>
                <label class="label"><span class="label-text text-sm">Contact Email</span></label>
                <input type="email" class="input input-sm input-bordered w-full passenger-email" placeholder="Email">
            </div>
            <div>
                <label class="label"><span class="label-text text-sm">Contact Mobile</span></label>
                <input type="text" class="input input-sm input-bordered w-full passenger-phone" placeholder="Mobile">
            </div>
        </div>
    `;
    container.appendChild(card);
    // Pre-fill contact from settings
    loadAgencySettingsToForm();
}

function removePassenger(id) {
    const el = document.getElementById('passenger-' + id);
    if (el) el.remove();
    renumberPassengers();
}

function renumberPassengers() {
    const cards = document.querySelectorAll('#passengers-container .passenger-card');
    cards.forEach((card, i) => {
        const label = card.querySelector('.text-primary');
        if (label) label.textContent = 'Passenger ' + (i + 1);
    });
}

// ===== PREVIEW TICKET =====
function previewTicket() {
    // Collect passenger data
    const cards = document.querySelectorAll('#passengers-container .passenger-card');
    const passengers = [];
    cards.forEach(card => {
        passengers.push({
            name: card.querySelector('.passenger-name')?.value || '',
            ticketNo: card.querySelector('.passenger-ticket')?.value || '',
            email: card.querySelector('.passenger-email')?.value || '',
            phone: card.querySelector('.passenger-phone')?.value || ''
        });
    });

    if (!passengers.length || !passengers[0].name) {
        showToast('Please enter at least one passenger name', 'error');
        return;
    }

    // Collect flight data
    const getValue = (id) => document.getElementById(id)?.value || '';
    const getSelectedText = (id) => {
        const sel = document.getElementById(id);
        return sel?.options[sel.selectedIndex]?.text || '';
    };

    const data = {
        passengers,
        pnr: getValue('booking-pnr'),
        travelDate: getValue('booking-travel-date'),
        returnDate: getValue('booking-return-date'),
        hasReturn: document.getElementById('booking-has-return')?.checked || false,
        fromCode: getValue('booking-from'),
        toCode: getValue('booking-to'),
        fromText: getSelectedText('booking-from'),
        toText: getSelectedText('booking-to'),
        departureTime: getValue('booking-departure'),
        arrivalTime: getValue('booking-arrival'),
        airlineCode: getValue('booking-airline'),
        airlineText: getSelectedText('booking-airline'),
        flightNumber: getValue('booking-flight-number'),
        baggage: getValue('booking-baggage')
    };

    // Submit via hidden form to open in new window
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'print/ticket.php';
    form.target = '_blank';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'ticketData';
    input.value = JSON.stringify(data);
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// ===== INVOICE LINE ITEMS =====
let invoiceItemCount = 1;

function addInvoiceItem() {
    invoiceItemCount++;
    const container = document.getElementById('invoice-items-container');
    const row = document.createElement('div');
    row.className = 'line-item-row';
    row.id = 'invoice-item-' + invoiceItemCount;
    row.innerHTML = `
        <div>
            <input type="text" class="input input-sm input-bordered w-full item-description" placeholder="Description">
        </div>
        <div>
            <input type="text" class="input input-sm input-bordered w-full item-passenger" placeholder="Passenger Name">
        </div>
        <div>
            <input type="date" class="input input-sm input-bordered w-full item-date">
        </div>
        <div>
            <input type="text" class="input input-sm input-bordered w-full item-pnr" placeholder="PNR">
        </div>
        <div>
            <input type="number" class="input input-sm input-bordered w-full item-amount" placeholder="0.00" step="0.01" oninput="calculateInvoiceTotal()">
        </div>
        <div>
            <button type="button" class="btn btn-ghost btn-sm text-error" onclick="removeInvoiceItem(${invoiceItemCount})">✕</button>
        </div>
    `;
    container.appendChild(row);
}

function removeInvoiceItem(id) {
    const el = document.getElementById('invoice-item-' + id);
    if (el) el.remove();
    calculateInvoiceTotal();
}

function calculateInvoiceTotal() {
    const amounts = document.querySelectorAll('.item-amount');
    let subtotal = 0;
    amounts.forEach(el => { subtotal += parseFloat(el.value) || 0; });

    const discountVal = parseFloat(document.getElementById('invoice-discount')?.value) || 0;
    const discountType = document.getElementById('invoice-discount-type')?.value || 'flat';
    const taxRate = parseFloat(document.getElementById('invoice-tax')?.value) || 0;

    const discount = discountType === 'percent' ? (subtotal * discountVal / 100) : discountVal;
    const afterDiscount = subtotal - discount;
    const tax = afterDiscount * taxRate / 100;
    const total = afterDiscount + tax;

    document.getElementById('invoice-subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('invoice-discount-amount').textContent = discount.toFixed(2);
    document.getElementById('invoice-tax-amount').textContent = tax.toFixed(2);
    document.getElementById('invoice-total').textContent = total.toFixed(2);
}

// ===== PREVIEW INVOICE =====
function previewInvoice() {
    const items = [];
    document.querySelectorAll('.line-item-row').forEach(row => {
        items.push({
            description: row.querySelector('.item-description')?.value || '',
            passenger: row.querySelector('.item-passenger')?.value || '',
            date: row.querySelector('.item-date')?.value || '',
            pnr: row.querySelector('.item-pnr')?.value || '',
            amount: parseFloat(row.querySelector('.item-amount')?.value) || 0
        });
    });

    const getValue = (id) => document.getElementById(id)?.value || '';
    
    // Get selected template
    const selectedTemplate = document.querySelector('input[name="invoice-template"]:checked')?.value || 'modern';

    const data = {
        invoiceNumber: getValue('invoice-number'),
        invoiceDate: getValue('invoice-date'),
        currency: getValue('invoice-currency'),
        customerName: getValue('invoice-customer-name'),
        customerAddress: getValue('invoice-customer-address'),
        customerPhone: getValue('invoice-customer-phone'),
        customerEmail: getValue('invoice-customer-email'),
        items,
        discount: parseFloat(getValue('invoice-discount')) || 0,
        discountType: getValue('invoice-discount-type'),
        tax: parseFloat(getValue('invoice-tax')) || 0,
        notes: getValue('invoice-notes'),
        template: selectedTemplate
    };

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'print/invoice.php';
    form.target = '_blank';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'invoiceData';
    input.value = JSON.stringify(data);
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// ===== DATABASE MANAGER =====
function switchDbTab(tab) {
    document.querySelectorAll('.db-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sub-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('db-' + tab).classList.remove('hidden');
    document.querySelector('[data-dbtab="' + tab + '"]').classList.add('active');
}

// Airlines CRUD
function loadAirlines() {
    fetch('api/airlines.php')
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('airlines-tbody');
            if (!tbody) return;
            tbody.innerHTML = data.map(a => `
                <tr>
                    <td class="font-mono font-bold">${a.code}</td>
                    <td>${a.name}</td>
                    <td>${a.country || ''}</td>
                    <td>
                        <button class="btn btn-ghost btn-xs text-info" onclick='editAirline(${JSON.stringify(a)})'>Edit</button>
                        <button class="btn btn-ghost btn-xs text-error" onclick="deleteAirline('${a.id}')">Delete</button>
                    </td>
                </tr>
            `).join('');
        });
}

function addAirline(event) {
    event.preventDefault();
    const code = document.getElementById('new-airline-code').value.toUpperCase();
    const name = document.getElementById('new-airline-name').value;
    const country = document.getElementById('new-airline-country').value;
    if (!code || !name) { showToast('Code and Name are required', 'error'); return; }

    fetch('api/airlines.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code, name, country })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Airline added!');
            document.getElementById('new-airline-code').value = '';
            document.getElementById('new-airline-name').value = '';
            document.getElementById('new-airline-country').value = '';
            loadAirlines();
        }
    })
    .catch(() => showToast('Error adding airline', 'error'));
}

function editAirline(airline) {
    const code = prompt('Airline Code:', airline.code);
    if (code === null) return;
    const name = prompt('Airline Name:', airline.name);
    if (name === null) return;
    const country = prompt('Country:', airline.country || '');
    if (country === null) return;

    fetch('api/airlines.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: airline.id, code: code.toUpperCase(), name, country })
    })
    .then(r => r.json())
    .then(data => { if (data.success) { showToast('Airline updated!'); loadAirlines(); } })
    .catch(() => showToast('Error updating airline', 'error'));
}

function deleteAirline(id) {
    if (!confirm('Delete this airline?')) return;
    fetch('api/airlines.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => { if (data.success) { showToast('Airline deleted!'); loadAirlines(); } })
    .catch(() => showToast('Error deleting airline', 'error'));
}

// Airports CRUD
function loadAirports() {
    fetch('api/airports.php')
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('airports-tbody');
            if (!tbody) return;
            tbody.innerHTML = data.map(a => `
                <tr>
                    <td class="font-mono font-bold">${a.code}</td>
                    <td>${a.city}</td>
                    <td>${a.name}</td>
                    <td>${a.country || ''}</td>
                    <td>
                        <button class="btn btn-ghost btn-xs text-info" onclick='editAirport(${JSON.stringify(a)})'>Edit</button>
                        <button class="btn btn-ghost btn-xs text-error" onclick="deleteAirport('${a.id}')">Delete</button>
                    </td>
                </tr>
            `).join('');
        });
}

function addAirport(event) {
    event.preventDefault();
    const code = document.getElementById('new-airport-code').value.toUpperCase();
    const city = document.getElementById('new-airport-city').value;
    const name = document.getElementById('new-airport-name').value;
    const country = document.getElementById('new-airport-country').value;
    if (!code || !city || !name) { showToast('Code, City, and Name are required', 'error'); return; }

    fetch('api/airports.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code, city, name, country })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Airport added!');
            document.getElementById('new-airport-code').value = '';
            document.getElementById('new-airport-city').value = '';
            document.getElementById('new-airport-name').value = '';
            document.getElementById('new-airport-country').value = '';
            loadAirports();
        }
    })
    .catch(() => showToast('Error adding airport', 'error'));
}

function editAirport(airport) {
    const code = prompt('Airport Code:', airport.code);
    if (code === null) return;
    const city = prompt('City:', airport.city);
    if (city === null) return;
    const name = prompt('Airport Name:', airport.name);
    if (name === null) return;
    const country = prompt('Country:', airport.country || '');
    if (country === null) return;

    fetch('api/airports.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: airport.id, code: code.toUpperCase(), city, name, country })
    })
    .then(r => r.json())
    .then(data => { if (data.success) { showToast('Airport updated!'); loadAirports(); } })
    .catch(() => showToast('Error updating airport', 'error'));
}

function deleteAirport(id) {
    if (!confirm('Delete this airport?')) return;
    fetch('api/airports.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => { if (data.success) { showToast('Airport deleted!'); loadAirports(); } })
    .catch(() => showToast('Error deleting airport', 'error'));
}

// Search/filter for database tables
function filterTable(inputId, tbodyId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    const rows = document.querySelectorAll('#' + tbodyId + ' tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

// Load database data on tab switch
document.addEventListener('DOMContentLoaded', () => {
    loadAirlines();
    loadAirports();
});
