<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Measurement;
use App\Http\Resources\Measurements\MeasurementResource;
use Illuminate\Support\Str;

class MeasurementController extends Controller
{
    public function index()
    {
        // $user = auth()->user();

        // if(in_array("Administrator", $user->measurements->pluck("name")->toArray())){
            return MeasurementResource::collection(Measurement::orderBy('created_at', 'desc')->get());
        // };
    }

    public function store(Request $request)
    {
        $measurement = Measurement::where('name', $request->name)->first();

        if($measurement) return response()->json([
            'status' => 'error',
            'message' => 'Measurement already exists.',
        ]);

        Measurement::create([
            'name' => $request->name,
            'user_id' => auth()->user()->id,
            'uuid' => Str::uuid()->toString()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Measurement created successfully.',
            'name' => $request->name
        ]);
    }

    public function update(Request $request)
    {
        $measurement = Measurement::where("uuid", $request->uuid)->first();

        if(!$measurement) return response()->json([
            'status' => 'error',
            'message' => 'Measurement does not exists.',
        ]);

        $measurement->update([
            'name' => $request->name,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Measurement updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $measurement = Measurement::where("uuid", $request->uuid)->first();
        $measurement->delete();

        return response()->json([
            'message' => 'Measurement deleted successfully.',
            'status' => 'success'
        ]);
    }
}
