<?php
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $airlines = getAirlines();
        respond(['success' => true, 'data' => $airlines]);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['code']) || empty($input['name'])) {
            respond(['error' => 'Code and Name are required'], 400);
        }
        $airlines = getAirlines();
        $newAirline = [
            'id' => uniqid(),
            'code' => strtoupper(trim($input['code'])),
            'name' => trim($input['name']),
            'country' => trim($input['country'] ?? '')
        ];
        $airlines[] = $newAirline;
        saveJSON('airlines.json', $airlines);
        respond(['success' => true, 'airline' => $newAirline]);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id'])) {
            respond(['error' => 'ID is required'], 400);
        }
        $airlines = getAirlines();
        $found = false;
        foreach ($airlines as &$airline) {
            if ($airline['id'] === $input['id']) {
                if (isset($input['code'])) $airline['code'] = strtoupper(trim($input['code']));
                if (isset($input['name'])) $airline['name'] = trim($input['name']);
                if (isset($input['country'])) $airline['country'] = trim($input['country']);
                $found = true;
                break;
            }
        }
        unset($airline);
        if (!$found) {
            respond(['error' => 'Airline not found'], 404);
        }
        saveJSON('airlines.json', $airlines);
        respond(['success' => true]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id'])) {
            respond(['error' => 'ID is required'], 400);
        }
        $airlines = getAirlines();
        $airlines = array_values(array_filter($airlines, fn($a) => $a['id'] !== $input['id']));
        saveJSON('airlines.json', $airlines);
        respond(['success' => true]);
        break;

    default:
        respond(['error' => 'Method not allowed'], 405);
}
