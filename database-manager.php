<?php
$airlines = getAirlines();
$airports = getAirports();
usort($airlines, fn($a, $b) => strcmp($a['name'], $b['name']));
usort($airports, fn($a, $b) => strcmp($a['city'], $b['city']));
?>

<!-- Sub-tabs for Airlines and Airports -->
<div class="tabs tabs-boxed mb-4">
    <a class="tab tab-active" id="tabAirlines" onclick="switchDbTab('airlines')">✈️ Airlines (<?= count($airlines) ?>)</a>
    <a class="tab" id="tabAirports" onclick="switchDbTab('airports')">🏢 Airports (<?= count($airports) ?>)</a>
</div>

<!-- Airlines Section -->
<div id="airlinesSection">
    <!-- Add New Airline -->
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <h4 class="font-bold text-sm mb-2">Add New Airline</h4>
            <div class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Code</span></label>
                    <input type="text" id="newAirlineCode" class="input input-sm input-bordered w-24" placeholder="e.g. EK" maxlength="3" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Name</span></label>
                    <input type="text" id="newAirlineName" class="input input-sm input-bordered w-48" placeholder="e.g. Emirates" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Country</span></label>
                    <input type="text" id="newAirlineCountry" class="input input-sm input-bordered w-40" placeholder="e.g. UAE" />
                </div>
                <button class="btn btn-sm btn-primary" onclick="addAirline()">
                    <i class="fas fa-plus mr-1"></i> Add
                </button>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" id="airlineSearch" class="input input-sm input-bordered w-full md:w-64" placeholder="🔍 Search airlines..." oninput="filterAirlines()" />
    </div>

    <!-- Airlines Table -->
    <div class="overflow-x-auto">
        <table class="table table-sm table-zebra w-full" id="airlinesTable">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($airlines as $al): ?>
                <tr data-id="<?= htmlspecialchars($al['id']) ?>">
                    <td class="al-code"><?= htmlspecialchars($al['code']) ?></td>
                    <td class="al-name"><?= htmlspecialchars($al['name']) ?></td>
                    <td class="al-country"><?= htmlspecialchars($al['country'] ?? '') ?></td>
                    <td>
                        <button class="btn btn-xs btn-ghost text-info" onclick="editAirline(this)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-xs btn-ghost text-error" onclick="deleteAirline('<?= htmlspecialchars($al['id']) ?>')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Airports Section -->
<div id="airportsSection" class="hidden">
    <!-- Add New Airport -->
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <h4 class="font-bold text-sm mb-2">Add New Airport</h4>
            <div class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Code</span></label>
                    <input type="text" id="newAirportCode" class="input input-sm input-bordered w-24" placeholder="e.g. DXB" maxlength="4" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">City</span></label>
                    <input type="text" id="newAirportCity" class="input input-sm input-bordered w-40" placeholder="e.g. Dubai" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Airport Name</span></label>
                    <input type="text" id="newAirportName" class="input input-sm input-bordered w-56" placeholder="e.g. Dubai International" />
                </div>
                <div>
                    <label class="label py-0"><span class="label-text text-sm">Country</span></label>
                    <input type="text" id="newAirportCountry" class="input input-sm input-bordered w-40" placeholder="e.g. UAE" />
                </div>
                <button class="btn btn-sm btn-primary" onclick="addAirport()">
                    <i class="fas fa-plus mr-1"></i> Add
                </button>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" id="airportSearch" class="input input-sm input-bordered w-full md:w-64" placeholder="🔍 Search airports..." oninput="filterAirports()" />
    </div>

    <!-- Airports Table -->
    <div class="overflow-x-auto">
        <table class="table table-sm table-zebra w-full" id="airportsTable">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>City</th>
                    <th>Airport Name</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($airports as $ap): ?>
                <tr data-id="<?= htmlspecialchars($ap['id']) ?>">
                    <td class="ap-code"><?= htmlspecialchars($ap['code']) ?></td>
                    <td class="ap-city"><?= htmlspecialchars($ap['city']) ?></td>
                    <td class="ap-name"><?= htmlspecialchars($ap['name']) ?></td>
                    <td class="ap-country"><?= htmlspecialchars($ap['country'] ?? '') ?></td>
                    <td>
                        <button class="btn btn-xs btn-ghost text-info" onclick="editAirport(this)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-xs btn-ghost text-error" onclick="deleteAirport('<?= htmlspecialchars($ap['id']) ?>')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<dialog id="editModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg" id="editModalTitle">Edit</h3>
        <div id="editModalBody" class="py-4"></div>
        <div class="modal-action">
            <button class="btn btn-sm" onclick="document.getElementById('editModal').close()">Cancel</button>
            <button class="btn btn-sm btn-primary" id="editModalSave">Save</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
function switchDbTab(tab) {
    document.getElementById('tabAirlines').classList.toggle('tab-active', tab === 'airlines');
    document.getElementById('tabAirports').classList.toggle('tab-active', tab === 'airports');
    document.getElementById('airlinesSection').classList.toggle('hidden', tab !== 'airlines');
    document.getElementById('airportsSection').classList.toggle('hidden', tab !== 'airports');
}

// Airlines CRUD
function addAirline() {
    const code = document.getElementById('newAirlineCode').value.trim().toUpperCase();
    const name = document.getElementById('newAirlineName').value.trim();
    const country = document.getElementById('newAirlineCountry').value.trim();
    if (!code || !name) { alert('Code and Name are required'); return; }

    fetch('/api/airlines.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ code, name, country })
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error || 'Failed');
    });
}

function editAirline(btn) {
    const row = btn.closest('tr');
    const id = row.dataset.id;
    const code = row.querySelector('.al-code').textContent;
    const name = row.querySelector('.al-name').textContent;
    const country = row.querySelector('.al-country').textContent;

    document.getElementById('editModalTitle').textContent = 'Edit Airline';
    document.getElementById('editModalBody').innerHTML = `
        <div class="space-y-2">
            <div><label class="label py-0"><span class="label-text text-sm">Code</span></label>
                <input type="text" id="editCode" class="input input-sm input-bordered w-full" value="${code}" /></div>
            <div><label class="label py-0"><span class="label-text text-sm">Name</span></label>
                <input type="text" id="editName" class="input input-sm input-bordered w-full" value="${name}" /></div>
            <div><label class="label py-0"><span class="label-text text-sm">Country</span></label>
                <input type="text" id="editCountry" class="input input-sm input-bordered w-full" value="${country}" /></div>
        </div>`;
    document.getElementById('editModalSave').onclick = function() {
        fetch('/api/airlines.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id,
                code: document.getElementById('editCode').value.trim().toUpperCase(),
                name: document.getElementById('editName').value.trim(),
                country: document.getElementById('editCountry').value.trim()
            })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.error || 'Failed');
        });
    };
    document.getElementById('editModal').showModal();
}

function deleteAirline(id) {
    if (!confirm('Delete this airline?')) return;
    fetch('/api/airlines.php', {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id })
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error || 'Failed');
    });
}

function filterAirlines() {
    const q = document.getElementById('airlineSearch').value.toLowerCase();
    document.querySelectorAll('#airlinesTable tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}

// Airports CRUD
function addAirport() {
    const code = document.getElementById('newAirportCode').value.trim().toUpperCase();
    const city = document.getElementById('newAirportCity').value.trim();
    const name = document.getElementById('newAirportName').value.trim();
    const country = document.getElementById('newAirportCountry').value.trim();
    if (!code || !city || !name) { alert('Code, City and Name are required'); return; }

    fetch('/api/airports.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ code, city, name, country })
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error || 'Failed');
    });
}

function editAirport(btn) {
    const row = btn.closest('tr');
    const id = row.dataset.id;
    const code = row.querySelector('.ap-code').textContent;
    const city = row.querySelector('.ap-city').textContent;
    const name = row.querySelector('.ap-name').textContent;
    const country = row.querySelector('.ap-country').textContent;

    document.getElementById('editModalTitle').textContent = 'Edit Airport';
    document.getElementById('editModalBody').innerHTML = `
        <div class="space-y-2">
            <div><label class="label py-0"><span class="label-text text-sm">Code</span></label>
                <input type="text" id="editCode" class="input input-sm input-bordered w-full" value="${code}" /></div>
            <div><label class="label py-0"><span class="label-text text-sm">City</span></label>
                <input type="text" id="editCity" class="input input-sm input-bordered w-full" value="${city}" /></div>
            <div><label class="label py-0"><span class="label-text text-sm">Airport Name</span></label>
                <input type="text" id="editName" class="input input-sm input-bordered w-full" value="${name}" /></div>
            <div><label class="label py-0"><span class="label-text text-sm">Country</span></label>
                <input type="text" id="editCountry" class="input input-sm input-bordered w-full" value="${country}" /></div>
        </div>`;
    document.getElementById('editModalSave').onclick = function() {
        fetch('/api/airports.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id,
                code: document.getElementById('editCode').value.trim().toUpperCase(),
                city: document.getElementById('editCity').value.trim(),
                name: document.getElementById('editName').value.trim(),
                country: document.getElementById('editCountry').value.trim()
            })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.error || 'Failed');
        });
    };
    document.getElementById('editModal').showModal();
}

function deleteAirport(id) {
    if (!confirm('Delete this airport?')) return;
    fetch('/api/airports.php', {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id })
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error || 'Failed');
    });
}

function filterAirports() {
    const q = document.getElementById('airportSearch').value.toLowerCase();
    document.querySelectorAll('#airportsTable tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
