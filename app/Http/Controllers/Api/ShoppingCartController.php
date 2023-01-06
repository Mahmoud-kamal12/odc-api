<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{

    public function index()
    {
        $products = auth()->user()->cart;
        return response()->json(['data' => $products]);
    }

    public function store(Request $request)
    {
        $id = $request->get('product_id');
        $product = Plant::where('id',$id)->first();

        if (!$product){
            return redirect()->route('home');
        }
        $user_id = auth()->user()->id;
        $cart = ShoppingCart::where(['product_id' => $id, 'user_id' =>$user_id])->first();
        if ($cart){
            $cart->quantity += 1;
            $cart->save();
        }else{
            $data = [
                'quantity' => (int)$request->get('quantity') ?? 1,
                'product_id' => $id,
                'user_id' => $user_id
            ];
            ShoppingCart::create($data);
        }

        return response()->json(['msg' => 'added to cart successfully']);
    }

    public function update(Request $request, ShoppingCart $shoppingCart)
    {
        $q = $request->get('quantity');
        $shoppingCart->quantity = $q;
        $shoppingCart->save();
        return response()->json(['msg' => 'success'] , 200);
    }

    public function destroy(ShoppingCart $shoppingCart)
    {
        $shoppingCart->delete();
        return response()->json(['msg' => 'success'] , 200);
    }
}
