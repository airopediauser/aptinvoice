<?php
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $airports = getAirports();
        respond(['success' => true, 'data' => $airports]);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['code']) || empty($input['city']) || empty($input['name'])) {
            respond(['error' => 'Code, City and Name are required'], 400);
        }
        $airports = getAirports();
        $newAirport = [
            'id' => uniqid(),
            'code' => strtoupper(trim($input['code'])),
            'city' => trim($input['city']),
            'name' => trim($input['name']),
            'country' => trim($input['country'] ?? '')
        ];
        $airports[] = $newAirport;
        saveJSON('airports.json', $airports);
        respond(['success' => true, 'airport' => $newAirport]);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id'])) {
            respond(['error' => 'ID is required'], 400);
        }
        $airports = getAirports();
        $found = false;
        foreach ($airports as &$airport) {
            if ($airport['id'] === $input['id']) {
                if (isset($input['code'])) $airport['code'] = strtoupper(trim($input['code']));
                if (isset($input['city'])) $airport['city'] = trim($input['city']);
                if (isset($input['name'])) $airport['name'] = trim($input['name']);
                if (isset($input['country'])) $airport['country'] = trim($input['country']);
                $found = true;
                break;
            }
        }
        unset($airport);
        if (!$found) {
            respond(['error' => 'Airport not found'], 404);
        }
        saveJSON('airports.json', $airports);
        respond(['success' => true]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id'])) {
            respond(['error' => 'ID is required'], 400);
        }
        $airports = getAirports();
        $airports = array_values(array_filter($airports, fn($a) => $a['id'] !== $input['id']));
        saveJSON('airports.json', $airports);
        respond(['success' => true]);
        break;

    default:
        respond(['error' => 'Method not allowed'], 405);
}
