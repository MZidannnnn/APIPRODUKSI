<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkshopCoordinate;

class WorkshopCoordinateController extends Controller
{
    public function edit()
    {
        $coordinate = WorkshopCoordinate::first();
        if (!$coordinate) {
            $coordinate = WorkshopCoordinate::create([
                'latitude' => '-3.2994',
                'longitude' => '114.5933'
            ]);
        }
        return view('admin.workshop-coordinate.edit', compact('coordinate'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $coordinate = WorkshopCoordinate::first();
        if ($coordinate) {
            $coordinate->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        return redirect()->route('admin.workshop-coordinate.edit')
            ->with('success', 'Koordinat workshop berhasil diperbarui.');
    }
}
