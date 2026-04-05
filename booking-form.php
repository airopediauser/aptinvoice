<?php
$airlines = getAirlines();
$airports = getAirports();
usort($airlines, fn($a, $b) => strcmp($a['name'], $b['name']));
usort($airports, fn($a, $b) => strcmp($a['city'], $b['city']));
$settings = getAgencySettings();
?>

<!-- Agency Details Section -->
<div class="card bg-base-100 shadow-sm mb-4">
    <div class="card-body p-4">
        <h3 class="card-title text-sm font-bold mb-2"><i class="fas fa-building mr-2"></i>Agency Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="label py-0"><span class="label-text text-sm">Agency Name</span></label>
                <input type="text" id="bf_agencyName" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['agencyName'] ?? '') ?>" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Email</span></label>
                <input type="email" id="bf_agencyEmail" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Phone</span></label>
                <input type="text" id="bf_agencyPhone" class="input input-sm input-bordered w-full" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" />
            </div>
        </div>
    </div>
</div>

<!-- ENR/PNR & Flight Details -->
<div class="card bg-base-100 shadow-sm mb-4">
    <div class="card-body p-4">
        <h3 class="card-title text-sm font-bold mb-2"><i class="fas fa-plane mr-2"></i>Flight Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="label py-0"><span class="label-text text-sm">ENR/PNR Reference</span></label>
                <input type="text" id="bf_pnr" class="input input-sm input-bordered w-full" placeholder="e.g. ABC123" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Travel Date</span></label>
                <input type="date" id="bf_travelDate" class="input input-sm input-bordered w-full" />
            </div>
            <div>
                <label class="label py-0 flex items-center gap-2">
                    <span class="label-text text-sm">Return Date</span>
                    <input type="checkbox" id="bf_hasReturn" class="checkbox checkbox-xs" onchange="document.getElementById('bf_returnDate').disabled = !this.checked" />
                </label>
                <input type="date" id="bf_returnDate" class="input input-sm input-bordered w-full" disabled />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Baggage Allowance</span></label>
                <select id="bf_baggage" class="select select-sm select-bordered w-full">
                    <option value="15 KG">15 KG</option>
                    <option value="20 KG">20 KG</option>
                    <option value="23 KG" selected>23 KG</option>
                    <option value="25 KG">25 KG</option>
                    <option value="30 KG">30 KG</option>
                    <option value="35 KG">35 KG</option>
                    <option value="40 KG">40 KG</option>
                    <option value="45 KG">45 KG</option>
                    <option value="46 KG">46 KG</option>
                    <option value="Two Pieces">Two Pieces</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div>
                <label class="label py-0"><span class="label-text text-sm">From Airport</span></label>
                <select id="bf_fromAirport" class="select select-sm select-bordered w-full">
                    <option value="">-- Select --</option>
                    <?php foreach ($airports as $ap): ?>
                    <option value="<?= htmlspecialchars($ap['code']) ?>" data-city="<?= htmlspecialchars($ap['city']) ?>" data-name="<?= htmlspecialchars($ap['name']) ?>">
                        <?= htmlspecialchars($ap['city']) ?> (<?= htmlspecialchars($ap['code']) ?>) - <?= htmlspecialchars($ap['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">To Airport</span></label>
                <select id="bf_toAirport" class="select select-sm select-bordered w-full">
                    <option value="">-- Select --</option>
                    <?php foreach ($airports as $ap): ?>
                    <option value="<?= htmlspecialchars($ap['code']) ?>" data-city="<?= htmlspecialchars($ap['city']) ?>" data-name="<?= htmlspecialchars($ap['name']) ?>">
                        <?= htmlspecialchars($ap['city']) ?> (<?= htmlspecialchars($ap['code']) ?>) - <?= htmlspecialchars($ap['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Departure Time</span></label>
                <input type="time" id="bf_departureTime" class="input input-sm input-bordered w-full" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Arrival Time</span></label>
                <input type="time" id="bf_arrivalTime" class="input input-sm input-bordered w-full" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div>
                <label class="label py-0"><span class="label-text text-sm">Airline</span></label>
                <select id="bf_airline" class="select select-sm select-bordered w-full">
                    <option value="">-- Select --</option>
                    <?php foreach ($airlines as $al): ?>
                    <option value="<?= htmlspecialchars($al['code']) ?>" data-name="<?= htmlspecialchars($al['name']) ?>">
                        <?= htmlspecialchars($al['code']) ?> - <?= htmlspecialchars($al['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Flight Number</span></label>
                <input type="text" id="bf_flightNumber" class="input input-sm input-bordered w-full" placeholder="e.g. EK202" />
            </div>
        </div>
    </div>
</div>

<!-- Passengers Section -->
<div class="card bg-base-100 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="card-title text-sm font-bold"><i class="fas fa-users mr-2"></i>Passengers</h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="addPassenger()">
                <i class="fas fa-plus mr-1"></i> Add Passenger
            </button>
        </div>
        <div id="passengersContainer">
            <!-- Passenger 1 (default) -->
            <div class="passenger-row border rounded-lg p-3 mb-2" data-index="0">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-sm">Passenger 1</span>
                    <button type="button" class="btn btn-xs btn-ghost text-error remove-passenger-btn hidden" onclick="removePassenger(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="label py-0"><span class="label-text text-sm">Passenger Name</span></label>
                        <input type="text" class="input input-sm input-bordered w-full pax-name" placeholder="Full Name" />
                    </div>
                    <div>
                        <label class="label py-0"><span class="label-text text-sm">Ticket Number</span></label>
                        <input type="text" class="input input-sm input-bordered w-full pax-ticket" placeholder="e.g. 1234567890" />
                    </div>
                    <div>
                        <label class="label py-0"><span class="label-text text-sm">Contact Email</span></label>
                        <input type="email" class="input input-sm input-bordered w-full pax-email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" />
                    </div>
                    <div>
                        <label class="label py-0"><span class="label-text text-sm">Contact Mobile</span></label>
                        <input type="text" class="input input-sm input-bordered w-full pax-mobile" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Button -->
<div class="text-center mt-4">
    <button type="button" class="btn btn-primary btn-wide" onclick="previewTicket()">
        <i class="fas fa-eye mr-2"></i> Preview Ticket
    </button>
</div>

<!-- Hidden form for POST to print page -->
<form id="ticketPostForm" action="/print/ticket.php" method="POST" target="_blank" style="display:none;">
    <input type="hidden" name="ticketData" id="ticketDataInput" />
</form>

<script>
let passengerCount = 1;

function addPassenger() {
    passengerCount++;
    const container = document.getElementById('passengersContainer');
    const div = document.createElement('div');
    div.className = 'passenger-row border rounded-lg p-3 mb-2';
    div.dataset.index = passengerCount - 1;
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="font-semibold text-sm">Passenger ${passengerCount}</span>
            <button type="button" class="btn btn-xs btn-ghost text-error" onclick="removePassenger(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="label py-0"><span class="label-text text-sm">Passenger Name</span></label>
                <input type="text" class="input input-sm input-bordered w-full pax-name" placeholder="Full Name" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Ticket Number</span></label>
                <input type="text" class="input input-sm input-bordered w-full pax-ticket" placeholder="e.g. 1234567890" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Contact Email</span></label>
                <input type="email" class="input input-sm input-bordered w-full pax-email" value="${document.getElementById('bf_agencyEmail').value}" />
            </div>
            <div>
                <label class="label py-0"><span class="label-text text-sm">Contact Mobile</span></label>
                <input type="text" class="input input-sm input-bordered w-full pax-mobile" value="${document.getElementById('bf_agencyPhone').value}" />
            </div>
        </div>
    `;
    container.appendChild(div);
    updateRemoveButtons();
}

function removePassenger(btn) {
    btn.closest('.passenger-row').remove();
    // Renumber passengers
    const rows = document.querySelectorAll('.passenger-row');
    rows.forEach((row, i) => {
        row.querySelector('.font-semibold').textContent = `Passenger ${i + 1}`;
        row.dataset.index = i;
    });
    passengerCount = rows.length;
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.passenger-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-passenger-btn, .btn-ghost.text-error');
        if (btn) {
            if (rows.length <= 1) {
                btn.classList.add('hidden');
            } else {
                btn.classList.remove('hidden');
            }
        }
    });
}

function previewTicket() {
    // Collect passengers
    const passengers = [];
    document.querySelectorAll('.passenger-row').forEach(row => {
        passengers.push({
            name: row.querySelector('.pax-name').value,
            ticketNumber: row.querySelector('.pax-ticket').value,
            email: row.querySelector('.pax-email').value,
            mobile: row.querySelector('.pax-mobile').value
        });
    });

    // Collect flight details
    const fromSelect = document.getElementById('bf_fromAirport');
    const toSelect = document.getElementById('bf_toAirport');
    const airlineSelect = document.getElementById('bf_airline');

    const fromOption = fromSelect.options[fromSelect.selectedIndex];
    const toOption = toSelect.options[toSelect.selectedIndex];
    const airlineOption = airlineSelect.options[airlineSelect.selectedIndex];

    const data = {
        agency: {
            name: document.getElementById('bf_agencyName').value,
            email: document.getElementById('bf_agencyEmail').value,
            phone: document.getElementById('bf_agencyPhone').value
        },
        flight: {
            pnr: document.getElementById('bf_pnr').value,
            travelDate: document.getElementById('bf_travelDate').value,
            hasReturn: document.getElementById('bf_hasReturn').checked,
            returnDate: document.getElementById('bf_returnDate').value,
            fromCode: fromSelect.value,
            fromCity: fromOption ? fromOption.dataset.city || '' : '',
            fromName: fromOption ? fromOption.dataset.name || '' : '',
            toCode: toSelect.value,
            toCity: toOption ? toOption.dataset.city || '' : '',
            toName: toOption ? toOption.dataset.name || '' : '',
            departureTime: document.getElementById('bf_departureTime').value,
            arrivalTime: document.getElementById('bf_arrivalTime').value,
            airlineCode: airlineSelect.value,
            airlineName: airlineOption ? airlineOption.dataset.name || '' : '',
            flightNumber: document.getElementById('bf_flightNumber').value,
            baggage: document.getElementById('bf_baggage').value
        },
        passengers: passengers
    };

    // Submit via hidden form
    document.getElementById('ticketDataInput').value = JSON.stringify(data);
    document.getElementById('ticketPostForm').submit();
}
</script>
