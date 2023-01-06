<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PlantController extends Controller
{
    public function index()
    {
        $products = Plant::orderBy('id', 'DESC')->paginate(env('LIMIT'));

        return response()->json(['data' => $products]);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name' , 'price' , 'description']);
        if ($request->has('image') && $request->file('image')){
            $data['image'] = 'uploads/'.Storage::disk('public')->putFile('products',$request->file('image'));
        }else if ($request->has('image')){
            $data['image'] = $request->get('image');
        }
        else{
            $data['image'] = null;
        }

        $plant = Plant::create($data);
        return response()->json(['msg' =>  'added successfully', 'data' => $plant] , 200);

    }

    public function show($product)
    {
        $product = Plant::findOrFail($product);

        return response()->json(['data' => $product]);
    }

    public function update(Request $request, Plant $plant)
    {
        $data = $request->only(['name' , 'price','description']);
        if ($request->has('image') && $request->file('image')){
            $data['main_image'] = 'uploads/'.Storage::disk('public')->putFile('products',$request->file('image'));
        }else if ($request->has('image')){
            $data['image'] = $request->get('image');
        }else{
            unset($data['image']);
        }

        $plant = $plant->update($data);
        return response()->json(['msg' =>  'updated successfully', 'data' => $plant] , 200);
    }

    public function destroy(Plant $plant)
    {
        $cover = $plant->image;
        $plant->delete();
        File::delete(public_path($cover));
        $msg = 'Planet deleted successfully';
        return response()->json(['msg' =>  $msg] , 200);
    }
}
