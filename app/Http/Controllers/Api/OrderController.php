<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plant;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $orders  = new Order();
        if ($request->has('q')){
            $q = $request->get('q');
            $orders = $orders->where(function ($qu) use($q){
                $qu->orWhere('email' ,'like' , "%{$q}%")
                ->orWhere('city' ,'like' , "%{$q}%")
                ->orWhere('zip','like' , "%{$q}%")
                ->orWhere('country','like' , "%{$q}%")
                ->orWhere('name','like' , "%{$q}%")
                ->orWhere('address','like' , "%{$q}%")
                ->orWhere('phone','like' , "%{$q}%");
            });

        }
        $orders = $orders->orderBy('id', 'DESC')->paginate(env('LIMIT'));
        return response()->json(['data' => $orders]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $user= auth()->user();
            $data = $request->all('email','city','zip','country','name','total','address','phone','payment_method','status');
            $data['user_id'] = $user->id;
            $order = Order::create($data);
            $total = 0;
            if ($order){
                $cart = ShoppingCart::where('user_id' , $user->id)->get();
                foreach ($cart as $product){
                    $originProduct = Plant::where('id' , $product->id)->first();
                    if (!$originProduct)
                        throw new \Exception('quantity not match');
                    $dataItem = [
                        'quantity' => $product->quantity,
                        'product_name' => $product->product->name,
                        'product_price' => $product->product->price,
                        'discount' => 0,
                        'extra' => 0,
                        'order_id' => $order->id,
                        'product_id' => $product->product_id,
                    ];
                    $item = OrderItem::create($dataItem);
                    if ($item){
                        $total += (($item->product_price * $item->quantity) + $item->extra)	- $item->discount;
                        $product->delete();
//                        $originProduct->quantity -=  $product->quantity;
                        $originProduct->save();
                    }
                }
            }
            $order->update(['total' => $total]);
            DB::commit();
            return response()->json(['msg' => 'added' , 'data' => $order]);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        return response()->json(['data' => $order->load('items')]);
    }

    public function destroy(Order $order)
    {
        $items = $order->items;
        $items->each->delete();
        $order->delete();
        return response()->json(['msg' => 'order deleted successfully']);
    }

    public  function oldOrders(){
        $orders = auth()->user()->orders;
        return response()->json(['data' => $orders->load('items')]);
    }

}
