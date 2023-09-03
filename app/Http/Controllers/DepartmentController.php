<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Resources\Departments\DepartmentResource;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if(in_array("Administration", $user->departments->pluck("name")->toArray())){
            return DepartmentResource::collection(Department::orderBy('created_at', 'desc')->get());
        } else {
            return DepartmentResource::collection($user->departments);
        };
    }

    public function store(Request $request)
    {
        $department = Department::where(['name'=> $request->name])->first();

        if($department) return response()->json([
            'status' => 'error',
            'message' => 'Department already exists.',
        ]);

        Department::create([
            'name' => $request->name,
            'uuid' => Str::uuid()->toString()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Department created successfully.',
        ]);
    }

    public function update(Request $request)
    {
        $department = Department::where("uuid", $request->uuid)->first();

        if(!$department) return response()->json([
            'status' => 'error',
            'message' => 'Department does not exists.',
        ]);

        $department->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Department updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $department = Department::where("uuid", $request->uuid)->first();
        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully.',
            'status' => 'success'
        ]);
    }
}
