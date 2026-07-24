<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BanDoController extends Controller
{
    /**
     * Proxy định vị tọa độ từ địa chỉ chuỗi
     */
    public function geocode(Request $request)
    {
        $address = $request->query('address');
        
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Vui lòng cung cấp địa chỉ.'], 400);
        }

        $apiKey = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');

        if (!$apiKey) {
            // OpenStreetMap Nominatim fallback (Miễn phí không cần API Key)
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'QuanMoiApp/1.0'
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1
                ]);

                if ($response->successful() && count($response->json()) > 0) {
                    $item = $response->json()[0];
                    return response()->json([
                        'success' => true,
                        'lat' => (float) $item['lat'],
                        'lng' => (float) $item['lon'],
                        'display_name' => $item['display_name'],
                        'provider' => 'openstreetmap'
                    ]);
                }
            } catch (\Exception $e) {
                // Fallback coordinates (Hồ Chí Minh center)
            }

            return response()->json([
                'success' => true,
                'lat' => 10.7769,
                'lng' => 106.7009,
                'display_name' => $address,
                'provider' => 'default'
            ]);
        }

        // Google Maps Geocoding API if key is configured
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                $loc = $response->json('results.0.geometry.location');
                return response()->json([
                    'success' => true,
                    'lat' => $loc['lat'],
                    'lng' => $loc['lng'],
                    'provider' => 'google'
                ]);
            }
        } catch (\Exception $e) {
            // Suppress
        }

        return response()->json(['success' => false, 'message' => 'Không thể định vị địa chỉ.'], 404);
    }
}
