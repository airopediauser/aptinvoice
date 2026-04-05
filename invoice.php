<?php
require_once __DIR__ . '/../settings.php';
$settings = getAgencySettings();

$data = null;
if (isset($_POST['invoiceData'])) {
    $data = json_decode($_POST['invoiceData'], true);
}
if (!$data) {
    echo '<p style="text-align:center;padding:40px;font-family:sans-serif;">No invoice data received. Please go back and try again.</p>';
    exit;
}

$agencyName = htmlspecialchars($settings['agencyName'] ?? 'Travel Agency');
$logoPath = !empty($settings['logoPath']) ? '../data/uploads/' . $settings['logoPath'] : '';
$stampPath = !empty($settings['stampPath']) ? '../data/uploads/' . $settings['stampPath'] : '';
$signaturePath = !empty($settings['signaturePath']) ? '../data/uploads/' . $settings['signaturePath'] : '';
$agencyEmail = htmlspecialchars($settings['email'] ?? '');
$agencyPhone = htmlspecialchars($settings['phone'] ?? '');
$agencyAddress = htmlspecialchars($settings['address'] ?? '');

$template = htmlspecialchars($data['template'] ?? 'modern');
$invoiceNumber = htmlspecialchars($data['invoiceNumber'] ?? '');
$invoiceDate = htmlspecialchars($data['invoiceDate'] ?? '');
$currency = htmlspecialchars($data['currency'] ?? 'OMR');
$customerName = htmlspecialchars($data['customerName'] ?? '');
$customerAddress = htmlspecialchars($data['customerAddress'] ?? '');
$customerPhone = htmlspecialchars($data['customerPhone'] ?? '');
$customerEmail = htmlspecialchars($data['customerEmail'] ?? '');
$items = $data['items'] ?? [];
$discountValue = floatval($data['discountValue'] ?? 0);
$discountType = $data['discountType'] ?? 'flat';
$taxPercent = floatval($data['taxPercent'] ?? 0);
$notes = htmlspecialchars($data['notes'] ?? '');

// Calculate totals
$subtotal = 0;
foreach ($items as $item) { $subtotal += floatval($item['amount'] ?? 0); }
$discount = $discountType === 'percent' ? ($subtotal * $discountValue / 100) : $discountValue;
if ($discount > $subtotal) $discount = $subtotal;
$afterDiscount = $subtotal - $discount;
$tax = $afterDiscount * $taxPercent / 100;
$total = $afterDiscount + $tax;

// WhatsApp message
$waText = "Invoice: $invoiceNumber\nDate: $invoiceDate\nCustomer: $customerName\nTotal: $currency " . number_format($total, 2) . "\n\nItems:\n";
foreach ($items as $i => $item) {
    $waText .= ($i+1) . ". " . ($item['description'] ?? '') . " - " . ($item['passenger'] ?? '') . " - $currency " . number_format(floatval($item['amount'] ?? 0), 2) . "\n";
}
if ($notes) $waText .= "\nNotes: $notes";
$waUrl = 'https://wa.me/?text=' . rawurlencode($waText);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?= $invoiceNumber ?> - <?= $agencyName ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, Helvetica, sans-serif; background: #f0f2f5; color: #1f2937; padding: 20px; }

  .no-print-bar { max-width: 800px; margin: 0 auto 16px; display: flex; gap: 10px; flex-wrap: wrap; }
  .btn { padding: 8px 20px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
  .btn-primary { background: #2563eb; color: #fff; }
  .btn-primary:hover { background: #1d4ed8; }
  .btn-ghost { background: #e5e7eb; color: #374151; }
  .btn-ghost:hover { background: #d1d5db; }
  .btn-whatsapp { background: #25d366; color: #fff; }
  .btn-whatsapp:hover { background: #1ebe5d; }

  .invoice-page {
    max-width: 800px; margin: 0 auto; background: #fff;
    border-radius: 8px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  }

  /* ===== HEADER STYLES ===== */
  .inv-header { padding: 28px 32px; display: flex; justify-content: space-between; align-items: flex-start; }
  .inv-header-left { display: flex; align-items: center; gap: 14px; }
  .inv-header-left img { height: 72px; width: auto; object-fit: contain; }
  .inv-header-left .agency-block .name { font-size: 22px; font-weight: 800; }
  .inv-header-left .agency-block .contact { font-size: 11px; opacity: 0.8; margin-top: 4px; }
  .inv-header-right { text-align: right; }
  .inv-header-right .inv-title { font-size: 28px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
  .inv-header-right .inv-meta { font-size: 12px; margin-top: 6px; opacity: 0.85; line-height: 1.8; }

  /* ===== BILL TO ===== */
  .bill-to { padding: 20px 32px; }
  .bill-to h4 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
  .bill-to .customer-name { font-size: 16px; font-weight: 700; }
  .bill-to .customer-detail { font-size: 12px; color: #6b7280; line-height: 1.7; }

  /* ===== TABLE ===== */
  .inv-table-wrap { padding: 0 32px 20px; }
  .inv-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .inv-table th { padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }
  .inv-table td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; }
  .inv-table .amount-col { text-align: right; }
  .inv-table tbody tr:last-child td { border-bottom: none; }

  /* ===== TOTALS ===== */
  .inv-totals { padding: 0 32px 24px; display: flex; justify-content: flex-end; }
  .inv-totals-box { width: 280px; }
  .inv-totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
  .inv-totals-row.total-row { font-size: 18px; font-weight: 800; border-top: 2px solid #e5e7eb; padding-top: 10px; margin-top: 6px; }

  /* ===== STAMP & SIGNATURE ===== */
  .inv-stamp-sig { padding: 0 32px 20px; display: flex; justify-content: flex-end; gap: 40px; }
  .stamp-block, .sig-block { text-align: center; }
  .stamp-block img { height: 100px; width: auto; object-fit: contain; }
  .sig-block img { height: 80px; width: auto; object-fit: contain; }
  .stamp-block .lbl, .sig-block .lbl { font-size: 10px; color: #6b7280; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

  /* ===== NOTES ===== */
  .inv-notes { padding: 0 32px 20px; font-size: 12px; color: #6b7280; }
  .inv-notes strong { color: #374151; }

  /* ===== THANK YOU ===== */
  .inv-thankyou { text-align: center; padding: 16px 32px; font-size: 15px; font-style: italic; color: #6b7280; }

  /* ===== FOOTER BAR ===== */
  .inv-footer-bar { padding: 14px 32px; text-align: center; font-size: 11px; color: #fff; }

  /* ========== TEMPLATE: MODERN ========== */
  .template-modern .inv-header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: #fff; }
  .template-modern .inv-table th { background: #eff6ff; color: #1e40af; border-bottom: 2px solid #3b82f6; }
  .template-modern .inv-totals-row.total-row { color: #1e40af; border-top-color: #3b82f6; }
  .template-modern .bill-to h4 { color: #2563eb; }
  .template-modern .inv-footer-bar { background: linear-gradient(135deg, #1e40af, #3b82f6); }

  /* ========== TEMPLATE: CLASSIC ========== */
  .template-classic .inv-header { background: #1e293b; color: #fff; }
  .template-classic .inv-header-right .inv-title { color: #d4a843; }
  .template-classic .inv-table th { background: #1e293b; color: #d4a843; border-bottom: 2px solid #d4a843; }
  .template-classic .inv-totals-row.total-row { color: #1e293b; border-top-color: #d4a843; }
  .template-classic .bill-to h4 { color: #d4a843; }
  .template-classic .inv-footer-bar { background: #1e293b; }

  /* ========== TEMPLATE: MINIMAL ========== */
  .template-minimal .inv-header { background: #fafafa; color: #1f2937; border-bottom: 1px solid #e5e7eb; }
  .template-minimal .inv-header-right .inv-title { color: #374151; }
  .template-minimal .inv-table th { background: #f9fafb; color: #374151; border-bottom: 1px solid #d1d5db; }
  .template-minimal .inv-totals-row.total-row { color: #111827; border-top-color: #d1d5db; }
  .template-minimal .bill-to h4 { color: #6b7280; }
  .template-minimal .inv-footer-bar { background: #374151; }

  /* ========== TEMPLATE: ELEGANT ========== */
  .template-elegant .inv-header { background: linear-gradient(135deg, #4c1d95, #0d9488); color: #fff; }
  .template-elegant .inv-table th { background: #f5f3ff; color: #4c1d95; border-bottom: 2px solid #7c3aed; }
  .template-elegant .inv-totals-row.total-row { color: #4c1d95; border-top-color: #7c3aed; }
  .template-elegant .bill-to h4 { color: #7c3aed; }
  .template-elegant .inv-footer-bar { background: linear-gradient(135deg, #4c1d95, #0d9488); }

  @media print {
    body { margin: 0; padding: 10px; background: #fff; }
    .no-print { display: none !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    @page { margin: 8mm; size: A4; }
    .invoice-page { box-shadow: none; }
  }
</style>
</head>
<body>

<div class="no-print-bar no-print">
  <button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save PDF</button>
  <a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" class="btn btn-whatsapp">💬 Share via WhatsApp</a>
  <button class="btn btn-ghost" onclick="window.close()">← Back</button>
</div>

<div class="invoice-page template-<?= $template ?>">

  <!-- Header -->
  <div class="inv-header">
    <div class="inv-header-left">
      <?php if ($logoPath): ?><img src="<?= $logoPath ?>" alt="Logo"><?php endif; ?>
      <div class="agency-block">
        <div class="name"><?= $agencyName ?></div>
        <div class="contact"><?= $agencyEmail ?><?= $agencyPhone ? ' | ' . $agencyPhone : '' ?></div>
      </div>
    </div>
    <div class="inv-header-right">
      <div class="inv-title">Invoice</div>
      <div class="inv-meta">
        <strong>#</strong> <?= $invoiceNumber ?><br>
        <strong>Date:</strong> <?= $invoiceDate ?>
      </div>
    </div>
  </div>

  <!-- Bill To -->
  <div class="bill-to">
    <h4>Bill To</h4>
    <div class="customer-name"><?= $customerName ?></div>
    <div class="customer-detail">
      <?php if ($customerAddress): ?><?= $customerAddress ?><br><?php endif; ?>
      <?php if ($customerPhone): ?><?= $customerPhone ?><br><?php endif; ?>
      <?php if ($customerEmail): ?><?= $customerEmail ?><?php endif; ?>
    </div>
  </div>

  <!-- Items Table -->
  <div class="inv-table-wrap">
    <table class="inv-table">
      <thead>
        <tr>
          <th style="width:30px;">#</th>
          <th>Description</th>
          <th>Passenger</th>
          <th>Departure</th>
          <th>PNR</th>
          <th class="amount-col">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $item): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
          <td><?= htmlspecialchars($item['passenger'] ?? '') ?></td>
          <td><?= htmlspecialchars($item['departureDate'] ?? '') ?></td>
          <td><?= htmlspecialchars($item['pnr'] ?? '') ?></td>
          <td class="amount-col"><?= $currency ?> <?= number_format(floatval($item['amount'] ?? 0), 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Totals -->
  <div class="inv-totals">
    <div class="inv-totals-box">
      <div class="inv-totals-row">
        <span>Subtotal</span>
        <span><?= $currency ?> <?= number_format($subtotal, 2) ?></span>
      </div>
      <?php if ($discount > 0): ?>
      <div class="inv-totals-row" style="color:#dc2626;">
        <span>Discount<?= $discountType === 'percent' ? " ({$discountValue}%)" : '' ?></span>
        <span>- <?= $currency ?> <?= number_format($discount, 2) ?></span>
      </div>
      <?php endif; ?>
      <?php if ($taxPercent > 0): ?>
      <div class="inv-totals-row">
        <span>Tax (<?= $taxPercent ?>%)</span>
        <span><?= $currency ?> <?= number_format($tax, 2) ?></span>
      </div>
      <?php endif; ?>
      <div class="inv-totals-row total-row">
        <span>Total</span>
        <span><?= $currency ?> <?= number_format($total, 2) ?></span>
      </div>
    </div>
  </div>

  <!-- Stamp & Signature -->
  <?php if ($stampPath || $signaturePath): ?>
  <div class="inv-stamp-sig">
    <?php if ($stampPath): ?>
    <div class="stamp-block">
      <img src="<?= $stampPath ?>" alt="Stamp">
      <div class="lbl">Company Stamp</div>
    </div>
    <?php endif; ?>
    <?php if ($signaturePath): ?>
    <div class="sig-block">
      <img src="<?= $signaturePath ?>" alt="Signature">
      <div class="lbl">Authorized Signature</div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Notes -->
  <?php if ($notes): ?>
  <div class="inv-notes">
    <strong>Notes:</strong> <?= nl2br($notes) ?>
  </div>
  <?php endif; ?>

  <!-- Thank You -->
  <div class="inv-thankyou">Thank you for your business!</div>

  <!-- Footer Bar -->
  <div class="inv-footer-bar">
    <?= $agencyName ?> &nbsp;|&nbsp; <?= $agencyEmail ?> &nbsp;|&nbsp; <?= $agencyPhone ?>
  </div>

</div>

</body>
</html>
