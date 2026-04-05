<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al Falah Travels - Ticket & Invoice Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-base-200 min-h-screen">
    <div class="container mx-auto p-4 max-w-6xl">
        <div role="tablist" class="tabs tabs-lifted tabs-lg mb-4">
            <a role="tab" class="tab tab-active text-primary font-bold" data-tab="booking" onclick="switchTab('booking')">✈️ Booking</a>
            <a role="tab" class="tab text-secondary font-bold" data-tab="invoice" onclick="switchTab('invoice')">🧾 Invoice</a>
            <a role="tab" class="tab text-accent font-bold" data-tab="database" onclick="switchTab('database')">🗄️ Database</a>
            <a role="tab" class="tab text-info font-bold" data-tab="settings" onclick="switchTab('settings')">⚙️ Settings</a>
        </div>
        
        <div id="tab-booking" class="tab-content">
            <?php include 'includes/booking-form.php'; ?>
        </div>
        <div id="tab-invoice" class="tab-content hidden">
            <?php include 'includes/invoice-form.php'; ?>
        </div>
        <div id="tab-database" class="tab-content hidden">
            <?php include 'includes/database-manager.php'; ?>
        </div>
        <div id="tab-settings" class="tab-content hidden">
            <?php include 'includes/agency-settings.php'; ?>
        </div>
    </div>
    <script src="js/app.js"></script>
</body>
</html>
