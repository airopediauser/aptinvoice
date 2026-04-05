<?php

/**
 * Al Falah Travels - Utility Functions
 */

/**
 * Load a JSON file from the data/ directory
 */
function loadJSON($file) {
    $path = __DIR__ . '/../data/' . $file;
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return $data !== null ? $data : [];
}

/**
 * Save data as JSON to the data/ directory
 */
function saveJSON($file, $data) {
    $path = __DIR__ . '/../data/' . $file;
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Get agency settings with defaults
 */
function getAgencySettings() {
    $defaults = [
        'agencyName' => 'Al Falah Travels',
        'address1' => '',
        'address2' => '',
        'email' => '',
        'phone' => '',
        'website' => '',
        'logoPath' => '',
        'stampPath' => '',
        'signaturePath' => '',
        'termsAndConditions' => "• This is a computer-generated ticket.\n• Please verify all details before travel.\n• Baggage allowance is subject to airline policy.\n• The agency is not responsible for flight delays or cancellations.\n• Refund and cancellation charges may apply as per airline rules."
    ];

    $settings = loadJSON('agency-settings.json');
    if (empty($settings)) {
        return $defaults;
    }
    return array_merge($defaults, $settings);
}

/**
 * Get list of airlines
 */
function getAirlines() {
    return loadJSON('airlines.json');
}

/**
 * Get list of airports
 */
function getAirports() {
    return loadJSON('airports.json');
}

/**
 * Generate a unique ID
 */
function generateId() {
    return uniqid('', true);
}

/**
 * Handle file upload
 */
function handleFileUpload($inputName, $subdir) {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error.'];
    }

    $file = $_FILES[$inputName];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File size exceeds 2MB limit.'];
    }

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }

    $uploadDir = __DIR__ . '/../data/uploads/' . $subdir . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid() . '.' . $ext;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $relativePath = $subdir . '/' . $filename;
        return ['success' => true, 'path' => $relativePath];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}

/**
 * Send JSON response and exit
 */
function respond($data, $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
