<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Search",
 *     description="Search endpoints for external API data"
 * )
 */
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
     * 
     * @OA\Get(
     *     path="/search/name",
     *     summary="Search by name (Turner Mia)",
     *     description="Search for records with the name 'Turner Mia' in the external API",
     *     tags={"Search"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="count", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="NAMA", type="string", example="Turner Mia"),
     *                     @OA\Property(property="NIM", type="string", example="9352078461"),
     *                     @OA\Property(property="YMD", type="string", example="20230405"),
     *                     @OA\Property(property="ALAMAT", type="string", example="Apt. 398 36279 Langworth Trail, Lake Donatoville, TN 99242-0940")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch data from external API")
     *         )
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/search/nim",
     *     summary="Search by NIM (9352078461)",
     *     description="Search for records with the NIM '9352078461' in the external API",
     *     tags={"Search"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="count", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="NAMA", type="string", example="Turner Mia"),
     *                     @OA\Property(property="NIM", type="string", example="9352078461"),
     *                     @OA\Property(property="YMD", type="string", example="20230405"),
     *                     @OA\Property(property="ALAMAT", type="string", example="Apt. 398 36279 Langworth Trail, Lake Donatoville, TN 99242-0940")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch data from external API")
     *         )
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/search/ymd",
     *     summary="Search by YMD (20230405)",
     *     description="Search for records with the YMD '20230405' in the external API",
     *     tags={"Search"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="count", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="NAMA", type="string", example="Turner Mia"),
     *                     @OA\Property(property="NIM", type="string", example="9352078461"),
     *                     @OA\Property(property="YMD", type="string", example="20230405"),
     *                     @OA\Property(property="ALAMAT", type="string", example="Apt. 398 36279 Langworth Trail, Lake Donatoville, TN 99242-0940")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch data from external API")
     *         )
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/search",
     *     summary="Custom search with parameters",
     *     description="Search for records with customizable parameters in the external API",
     *     tags={"Search"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="nama",
     *         in="query",
     *         required=false,
     *         description="Name to search for",
     *         @OA\Schema(type="string", example="Turner Mia")
     *     ),
     *     @OA\Parameter(
     *         name="nim",
     *         in="query",
     *         required=false,
     *         description="NIM to search for",
     *         @OA\Schema(type="string", example="9352078461")
     *     ),
     *     @OA\Parameter(
     *         name="ymd",
     *         in="query",
     *         required=false,
     *         description="YMD to search for",
     *         @OA\Schema(type="string", example="20230405")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="count", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="NAMA", type="string", example="Turner Mia"),
     *                     @OA\Property(property="NIM", type="string", example="9352078461"),
     *                     @OA\Property(property="YMD", type="string", example="20230405"),
     *                     @OA\Property(property="ALAMAT", type="string", example="Apt. 398 36279 Langworth Trail, Lake Donatoville, TN 99242-0940")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="At least one search parameter (nama, nim, or ymd) is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch data from external API")
     *         )
     *     )
     * )
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
