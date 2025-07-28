<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\location;



class LocationController extends Controller
{// app/Http/Controllers/Api/LocationController.php
public function store(Request $request) {
    try {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        $validated['user_id'] = $request->user()->id;
        $location = Location::create($validated);
        return response()->json($location, 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al guardar la ubicación',
            'error' => $e->getMessage() 
        ], 500);
    }
}
}
