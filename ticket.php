<?php
require_once __DIR__ . '/../settings.php';
$settings = getAgencySettings();

$ticketData = null;
if (isset($_POST['ticketData'])) {
    $ticketData = json_decode($_POST['ticketData'], true);
}
if (!$ticketData) {
    echo '<p style="text-align:center;padding:40px;font-family:sans-serif;">No ticket data received. Please go back and try again.</p>';
    exit;
}

$agencyName = htmlspecialchars($settings['agencyName'] ?? 'Travel Agency');
$logoPath = !empty($settings['logoPath']) ? '../data/uploads/' . $settings['logoPath'] : '';
$address = htmlspecialchars($settings['address'] ?? '');
$address2 = htmlspecialchars($settings['address2'] ?? '');
$email = htmlspecialchars($settings['email'] ?? '');
$phone = htmlspecialchars($settings['phone'] ?? '');
$website = htmlspecialchars($settings['website'] ?? '');
$terms = htmlspecialchars($settings['termsConditions'] ?? 'This is a computer-generated document. Fares are subject to change. Please verify all details before travel.');

$passengers = $ticketData['passengers'] ?? [];
$from = htmlspecialchars($ticketData['from'] ?? '');
$fromAirport = htmlspecialchars($ticketData['fromAirport'] ?? '');
$to = htmlspecialchars($ticketData['to'] ?? '');
$toAirport = htmlspecialchars($ticketData['toAirport'] ?? '');
$airline = htmlspecialchars($ticketData['airline'] ?? '');
$flightNumber = htmlspecialchars($ticketData['flightNumber'] ?? '');
$departureDate = htmlspecialchars($ticketData['departureDate'] ?? '');
$departureTime = htmlspecialchars($ticketData['departureTime'] ?? '');
$arrivalTime = htmlspecialchars($ticketData['arrivalTime'] ?? '');
$duration = htmlspecialchars($ticketData['duration'] ?? '');
$classType = htmlspecialchars($ticketData['class'] ?? 'Economy');
$baggage = htmlspecialchars($ticketData['baggage'] ?? '23 KG');
$pnr = htmlspecialchars($ticketData['pnr'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket - <?= $agencyName ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, Helvetica, sans-serif; background: #f0f2f5; color: #1a1a2e; padding: 20px; }

  .no-print-bar { max-width: 780px; margin: 0 auto 16px; display: flex; gap: 10px; }
  .btn { padding: 8px 20px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
  .btn-primary { background: #2563eb; color: #fff; }
  .btn-primary:hover { background: #1d4ed8; }
  .btn-ghost { background: #e5e7eb; color: #374151; }
  .btn-ghost:hover { background: #d1d5db; }

  .ticket-card {
    max-width: 780px; margin: 0 auto 30px; background: #fff;
    border-radius: 12px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
  }

  .ticket-header {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    color: #fff; padding: 16px 24px;
    display: flex; justify-content: space-between; align-items: center;
  }
  .ticket-header-left { display: flex; align-items: center; gap: 12px; }
  .ticket-header-left img { height: 40px; width: auto; border-radius: 6px; background: #fff; padding: 2px; }
  .ticket-header-left .agency-name { font-size: 16px; font-weight: 700; }
  .ticket-header-right { font-size: 18px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; opacity: 0.95; }

  .ticket-body { padding: 24px; }

  .ticket-top { display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px dashed #e5e7eb; }
  .ticket-top .label { font-size: 10px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; margin-bottom: 2px; }
  .ticket-top .value { font-size: 14px; font-weight: 600; }

  .flight-route { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding: 16px 0; }
  .route-point { text-align: center; flex: 0 0 160px; }
  .route-point .city { font-size: 22px; font-weight: 800; color: #1e3a5f; }
  .route-point .airport { font-size: 11px; color: #6b7280; margin-top: 2px; }
  .route-point .time { font-size: 20px; font-weight: 700; color: #111827; margin-top: 6px; }
  .route-point .date { font-size: 11px; color: #6b7280; }

  .route-line { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 0 12px; }
  .route-line .line { width: 100%; height: 2px; background: linear-gradient(90deg, #2563eb, #93c5fd, #2563eb); position: relative; margin: 8px 0; }
  .route-line .plane-icon { color: #2563eb; }
  .route-line .duration { font-size: 11px; color: #6b7280; margin-top: 4px; }
  .route-line .airline-info { font-size: 12px; font-weight: 600; color: #2563eb; margin-top: 2px; }

  .details-row {
    display: flex; gap: 0; margin-bottom: 0;
    background: #f8fafc; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;
  }
  .detail-item { flex: 1; padding: 12px 16px; border-right: 1px solid #e5e7eb; }
  .detail-item:last-child { border-right: none; }
  .detail-item .label { font-size: 10px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; }
  .detail-item .value { font-size: 13px; font-weight: 600; margin-top: 2px; }

  .ticket-footer {
    background: #f8fafc; padding: 16px 24px; border-top: 1px solid #e5e7eb;
    text-align: center; font-size: 11px; color: #6b7280; line-height: 1.8;
  }
  .ticket-footer .terms { font-size: 9px; color: #9ca3af; margin-top: 8px; font-style: italic; }
  .ticket-footer-bar { height: 4px; background: linear-gradient(90deg, #1e3a5f, #2563eb, #1e3a5f); }

  @media print {
    body { margin: 0; padding: 10px; background: #fff; }
    .no-print { display: none !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    @page { margin: 8mm; size: A4; }
    .ticket-card { page-break-after: always; box-shadow: none; border: 1px solid #ccc; }
    .ticket-card:last-child { page-break-after: auto; }
  }
</style>
</head>
<body>

<div class="no-print-bar no-print">
  <button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save PDF</button>
  <button class="btn btn-ghost" onclick="window.close()">← Back</button>
</div>

<?php foreach ($passengers as $index => $pax):
  $paxName = htmlspecialchars($pax['name'] ?? 'PASSENGER');
  $ticketNo = htmlspecialchars($pax['ticketNo'] ?? '');
  $paxPnr = htmlspecialchars($pax['pnr'] ?? $pnr);
?>
<div class="ticket-card">
  <div class="ticket-header">
    <div class="ticket-header-left">
      <?php if ($logoPath): ?><img src="<?= $logoPath ?>" alt="Logo"><?php endif; ?>
      <span class="agency-name"><?= $agencyName ?></span>
    </div>
    <div class="ticket-header-right">E-Ticket</div>
  </div>

  <div class="ticket-body">
    <div class="ticket-top">
      <div>
        <div class="label">Passenger Name</div>
        <div class="value"><?= $paxName ?></div>
      </div>
      <div>
        <div class="label">Ticket No</div>
        <div class="value"><?= $ticketNo ?></div>
      </div>
      <div>
        <div class="label">PNR / ENR</div>
        <div class="value"><?= $paxPnr ?></div>
      </div>
      <div>
        <div class="label">Date</div>
        <div class="value"><?= $departureDate ?></div>
      </div>
    </div>

    <div class="flight-route">
      <div class="route-point">
        <div class="city"><?= $from ?></div>
        <div class="airport"><?= $fromAirport ?></div>
        <div class="time"><?= $departureTime ?></div>
        <div class="date"><?= $departureDate ?></div>
      </div>
      <div class="route-line">
        <div class="plane-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="#2563eb" style="transform: rotate(90deg);">
            <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
          </svg>
        </div>
        <div class="line"></div>
        <div class="duration"><?= $duration ?></div>
        <div class="airline-info"><?= $airline ?> <?= $flightNumber ?></div>
      </div>
      <div class="route-point">
        <div class="city"><?= $to ?></div>
        <div class="airport"><?= $toAirport ?></div>
        <div class="time"><?= $arrivalTime ?></div>
      </div>
    </div>

    <div class="details-row">
      <div class="detail-item">
        <div class="label">Baggage</div>
        <div class="value"><?= $baggage ?></div>
      </div>
      <div class="detail-item">
        <div class="label">Class</div>
        <div class="value"><?= $classType ?></div>
      </div>
      <div class="detail-item">
        <div class="label">Status</div>
        <div class="value" style="color:#059669;">Confirmed</div>
      </div>
      <div class="detail-item">
        <div class="label">Flight</div>
        <div class="value"><?= $airline ?> <?= $flightNumber ?></div>
      </div>
    </div>
  </div>

  <div class="ticket-footer">
    <?= $address ?><?= $address2 ? ' | ' . $address2 : '' ?><br>
    <?= $email ?><?= $phone ? ' | ' . $phone : '' ?><?= $website ? ' | ' . $website : '' ?>
    <div class="terms"><?= $terms ?></div>
  </div>
  <div class="ticket-footer-bar"></div>
</div>
<?php endforeach; ?>

</body>
</html>
