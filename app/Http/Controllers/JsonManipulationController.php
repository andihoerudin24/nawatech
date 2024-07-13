<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JsonManipulationController extends Controller
{
    public function manipulate()
    {
        // Load JSON files from storage
        $json1 = json_decode(Storage::disk('public')->get('data1.json'), true);
        $json2 = json_decode(Storage::disk('public')->get('data2.json'), true);

        // Manipulate the data
        $manipulatedData = $this->manipulateData($json1, $json2);

        // Sort the data by 'ahass_distance'
        usort($manipulatedData, function ($a, $b) {
            return $a['ahass_distance'] <=> $b['ahass_distance'];
        });

        // Return the manipulated and sorted data as JSON
        return response()->json([
            'status' => 1,
            'message' => 'Data Successfully Retrieved.',
            'data' => $manipulatedData
        ]);
    }

    private function manipulateData($json1, $json2)
    {
        $result = [];

        // Create a lookup array for workshop data
        $workshops = [];
        foreach ($json2['data'] as $workshop) {
            $workshops[$workshop['code']] = $workshop;
        }

        // Combine and manipulate JSON data according to the required structure
        foreach ($json1['data'] as $item) {
            $workshopCode = $item['booking']['workshop']['code'];
            $workshopData = $workshops[$workshopCode] ?? null;

            $result[] = [
                'name' => $item['name'],
                'email' => $item['email'],
                'booking_number' => $item['booking']['booking_number'],
                'book_date' => $item['booking']['book_date'],
                'ahass_code' => $workshopData['code'] ?? '',
                'ahass_name' => $workshopData['name'] ?? $item['booking']['workshop']['name'],
                'ahass_address' => $workshopData['address'] ?? '',
                'ahass_contact' => $workshopData['phone_number'] ?? '',
                'ahass_distance' => $workshopData['distance'] ?? 0,
                'motorcycle_ut_code' => $item['booking']['motorcycle']['ut_code'],
                'motorcycle' => $item['booking']['motorcycle']['name']
            ];
        }

        return $result;
    }
}
