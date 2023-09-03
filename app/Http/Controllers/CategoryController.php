<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Http\Resources\Categories\CategoryResource;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if(in_array("Administration", $user->departments->pluck("name")->toArray())){
            return CategoryResource::collection(Category::orderBy('created_at', 'desc')->get());
        } else {
            return CategoryResource::collection(Category::orderBy('created_at', 'desc')
                ->where('user_id', 1)
                ->orWhere('user_id', $user->id)
                ->get());
        };
    }

    public function store(Request $request)
    {
        $category = Category::where(['name'=> $request->name])->first();
        $user_id = User::where("uuid", $request->uuid)->first()->id;

        if($category) return response()->json([
            'status' => 'error',
            'message' => 'Category already exists.',
        ]);

        Category::create([
            'name' => $request->name,
            'user_id' => $user_id,
            'uuid' => Str::uuid()->toString()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.',
        ]);
    }

    public function update(Request $request)
    {
        $category = Category::where("uuid", $request->uuid)->first();

        if(!$category) return response()->json([
            'status' => 'error',
            'message' => 'Category does not exists.',
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $category = Category::where("uuid", $request->uuid)->first();
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
            'status' => 'success'
        ]);
    }
}
