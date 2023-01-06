<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LevelController extends Controller
{

    public function index()
    {
        $levels = Level::all();
        return response()->json(['data' => $levels]);

    }

    public function store(Request $request)
    {

        $data = $request->only(['name']);
        try {
            $level = Level::create($data);
            return response()->json(['msg'=> 'Level Added Successfully', 'data' => $level]);
        }catch (\Exception $e){
            dd($e->getMessage() , $e->getLine() , $e->getFile());
        }
    }

    public function update(Request $request, Level $level)
    {
        $data = $request->only(['name']);

        try {
           $level->update($data);
           return response()->json(['msg'=> 'Level Updated Successfully', 'data' => $level]);
        }catch (\Exception $e){
            dd($e->getMessage() , $e->getLine() , $e->getFile());
        }
    }

    public function destroy(Level $level)
    {
        $level->delete();
        return response()->json(['msg' => 'Level deleted successfully']);
    }

}
