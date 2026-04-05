<?php
require_once __DIR__ . '/../includes/functions.php';

// Handle GET - return current settings
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    respond(getAgencySettings());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Method not allowed'], 405);
}

$settings = getAgencySettings();

// Update text fields
$fields = ['agencyName', 'address1', 'address2', 'email', 'phone', 'website', 'termsAndConditions'];
foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        $settings[$field] = $_POST[$field];
    }
}

// Handle file uploads
$fileFields = ['logo' => 'logoPath', 'stamp' => 'stampPath', 'signature' => 'signaturePath'];
foreach ($fileFields as $inputName => $settingKey) {
    if (!empty($_FILES[$inputName]['name'])) {
        $result = handleFileUpload($inputName, $inputName . 's');
        if ($result['success']) {
            $settings[$settingKey] = $result['path'];
        }
    }
}

saveJSON('agency-settings.json', $settings);
respond(['success' => true, 'settings' => $settings]);
