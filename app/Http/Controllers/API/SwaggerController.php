<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    /**
     * Display Swagger UI page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('swagger.index');
    }

    /**
     * Return Swagger JSON definition
     *
     * @return \Illuminate\Http\Response
     */
    public function json()
    {
        $filePath = storage_path('api-docs/swagger.json');

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'Swagger JSON file not found'], 404);
        }

        $content = File::get($filePath);

        return (new Response($content, 200))
            ->header('Content-Type', 'application/json');
    }
}
