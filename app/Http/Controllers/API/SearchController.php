<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Base URL for external API
     */
    protected $apiUrl = 'https://ogienurdiana.com/career/ecc694ce4e7f6e45a5a7912cde9fe131';
    
    /**
     * Parse raw data from the API response
     *
     * @param string $rawData The raw data string from the API
     * @return array Parsed data as an array of objects
     */
    protected function parseApiData($rawData)
    {
        // Split the data by lines
        $lines = explode("\n", $rawData);
        
        // Get the headers from the first line
        $headers = explode('|', $lines[0]);
        
        // Parse the data rows
        $parsedData = [];
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            
            $values = explode('|', $line);
            $item = [];
            
            foreach ($headers as $j => $header) {
                $item[$header] = $values[$j] ?? null;
            }
            
            $parsedData[] = $item;
        }
        
        return $parsedData;
    }

    /**
     * Fetch data from external API
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    protected function fetchApiData()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get($this->apiUrl);
            
            if ($response->successful() && $response->json('RC') === 200) {
                return $this->parseApiData($response->json('DATA'));
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response->json('RCM') ?? 'Error retrieving data from external API',
                ], $response->json('RC') ?? 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data from external API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search data by name 'Turner Mia'
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByName()
    {
        $data = $this->fetchApiData();
        
        // If fetchApiData returned a JsonResponse, it means an error occurred
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }
        
        // Filter data for 'Turner Mia'
        $filteredData = array_filter($data, function($item) {
            return $item['NAMA'] === 'Turner Mia';
        });
        
        return response()->json([
            'status' => 'success',
            'count' => count($filteredData),
            'data' => array_values($filteredData)
        ], 200);
    }

    /**
     * Search data by NIM '9352078461'
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByNim()
    {
        $data = $this->fetchApiData();
        
        // If fetchApiData returned a JsonResponse, it means an error occurred
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }
        
        // Filter data for NIM '9352078461'
        $filteredData = array_filter($data, function($item) {
            return $item['NIM'] === '9352078461';
        });
        
        return response()->json([
            'status' => 'success',
            'count' => count($filteredData),
            'data' => array_values($filteredData)
        ], 200);
    }

    /**
     * Search data by YMD '20230405'
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByYmd()
    {
        $data = $this->fetchApiData();
        
        // If fetchApiData returned a JsonResponse, it means an error occurred
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }
        
        // Filter data for YMD '20230405'
        $filteredData = array_filter($data, function($item) {
            return $item['YMD'] === '20230405';
        });
        
        return response()->json([
            'status' => 'success',
            'count' => count($filteredData),
            'data' => array_values($filteredData)
        ], 200);
    }

    /**
     * Custom search with parameters
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $nama = $request->query('nama');
        $nim = $request->query('nim');
        $ymd = $request->query('ymd');
        
        // Ensure at least one search parameter is provided
        if (!$nama && !$nim && !$ymd) {
            return response()->json([
                'status' => 'error',
                'message' => 'At least one search parameter (nama, nim, or ymd) is required'
            ], 400);
        }
        
        $data = $this->fetchApiData();
        
        // If fetchApiData returned a JsonResponse, it means an error occurred
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }
        
        // Apply filters based on provided parameters
        $filteredData = $data;
        
        if ($nama) {
            $filteredData = array_filter($filteredData, function($item) use ($nama) {
                return strpos($item['NAMA'], $nama) !== false;
            });
        }
        
        if ($nim) {
            $filteredData = array_filter($filteredData, function($item) use ($nim) {
                return strpos($item['NIM'], $nim) !== false;
            });
        }
        
        if ($ymd) {
            $filteredData = array_filter($filteredData, function($item) use ($ymd) {
                return strpos($item['YMD'], $ymd) !== false;
            });
        }
        
        return response()->json([
            'status' => 'success',
            'count' => count($filteredData),
            'data' => array_values($filteredData)
        ], 200);
    }
}
