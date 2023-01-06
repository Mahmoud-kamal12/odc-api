<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePostRequest;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::select('title' , 'description')->orderBy('created_at')->paginate(25);
        return response()->json($posts);
    }

    public function store(StorePostRequest $request)
    {
        $request_data = $request->all();
        try {
            $request_data = $request->all();

            if ($request->has('image') && $request->image){
                $request_data['image'] = Storage::disk('public')->putFile('images/posts',$request->file('image'));
            }else{
                $request_data['image'] = "image.png";
            }

            if($data = Post::create($request_data)){
                $msg = 'Post added successfully';
                return response()->json(["success" => true , 'msg' =>  $msg, 'data' => $data->load('user')] , 200);
            };

        }catch (\Exception $e){
            return response()->json(["error" => $e->getMessage() , "line" => $e->getLine()],500);
        }
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->all();
        if ($request->has('image') && $request->file('image')){
            $data['image'] = 'uploads/'.Storage::disk('public')->putFile('images/posts',$request->file('image'));
        }else{
            unset($data['image']);
        }

        $post = $post->update($data);

        $msg = 'Post updated successfully';
        return response()->json(['msg' =>  $msg, 'data' => $post->load('user')] , 200);
    }

    public function show( int $id)
    {
        $post = Post::where('id',$id)->with('user')->first();
        return response()->json(['data' => $post]);
    }

    public function destroy(Post $post)
    {
        $image = $post->image;
        $post->delete();
        File::delete(public_path($image));
        $msg = 'Post deleted successfully';
        return response()->json(['msg' =>  $msg] , 200);
    }

}
