<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB; 
use App\User;
use App\Product;
use App\Order;
use App\Profile;
use App\OrderProduct;
use App\Address;
use Redirect;
use App\Review;

class HomeController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth');
    } 
    public function profile()
    {    
        $user = User::find(Auth::user()->id);
        $profile = DB::table("profiles")->where('userId',Auth::user()->id)->get();
        if(count($profile) == 0) {
            Profile::create([
                'userId' => Auth::user()->id,
                'image' => 'images/sample/avatar.png',
            ]);

            $profile = DB::table("profiles")->where('userId',Auth::user()->id)->get();
        }  
        $address = DB::table("addresses")->where('user_id', Auth::user()->id)->get();

        $order = DB::table('orders')
            ->join('users', 'orders.userId', '=', 'users.id') 
            ->join('addresses', 'orders.adrId', '=', 'addresses.id') 
            ->select('orders.*','users.*','addresses.*')  
            ->where("orders.orderPlaced", true)
            ->where('users.id','=',Auth::user()->id)
            ->get();  
        return view('auth.profile')->with('profile', $profile) 
                                ->with('address',$address)
                                ->with('orders',$order);
    }
    public function orders() {
        $order = DB::table('orders')
            ->join('users', 'orders.userId', '=', 'users.id') 
            ->join('addresses', 'orders.adrId', '=', 'addresses.id') 
            ->select('orders.*',"orders.id as orderId",'users.*','addresses.*')  
            ->where("orders.orderPlaced", true)
            ->where('users.id','=',Auth::user()->id)
            ->get();    
        return view("auth.orders")->with("orders", $order);
    }
    public function getProfile() {
        $profile = DB::table("profiles")->where('userId',Auth::user()->id)->get();
        return view('auth.editProfile')->with('profile', $profile);
    }
    public function address() {
        return view("auth.addAddress");
    }
    public function editProfile(Request $request) {
        $data = $request->all(); 
        $request->validate([ 
            'name' => ['required', 'alpha', 'min:3'],  
            'phoneNumber' => ['required','digits:10']
        ]);
        if($request['image'] != null) { 
            $request->validate([ 
                'image' => ['required','image','mimes:jpeg,png,jpg'],
            ]);
            $image = $request->file('image');
            $imageName = time().'.'.$data['image']->getClientOriginalExtension();
            $image->move('images/profiles', $imageName);

            DB::table('profiles')
                ->where('userId' ,'=', Auth::user()->id)
                ->update(['image' => 'images/profiles/'.$imageName]);
        }

        DB::table('profiles')
            ->where('userId' ,'=', Auth::user()->id)
            ->update(['phoneNumber' => $data['phoneNumber']]);
        DB::table('users')
            ->where('id' ,'=', Auth::user()->id)
            ->update(['name' => $data['name']]);
        
        return Redirect::route('profile',1)->with('message', 'Profile Updated Succesfully'); 
    }
    public function addAddress(Request $request) {
        $request->validate([ 
            'address' => ['required', 'string', 'min:5'],  
            'phoneNumber' => ['required','digits:10']
        ]);  
        $data = $request->all();
        Address::create([
            'user_id' => Auth::user()->id,
            'address' => $data['address'],
            'phoneNumber' => $data['phoneNumber']
        ]); 
        return Redirect::route('profile')->with('message', 'Address Added Succesfully');
    }
    public function updateAddress(Request $request, $id) {
        $request->validate([ 
            'address' => ['required', 'string', 'min:5'],  
            'phoneNumber' => ['required','digits:10']
        ]);  
        $data = $request->all();
        DB::table("addresses")
        ->where("id",$id)
        ->update([ 
            'address' => $data['address'],
            'phoneNumber' => $data['phoneNumber']
        ]);

        return Redirect::route('profile')->with('message', 'Address Updated Succesfully');
    }
    public function editAddress($id) {
        $data = Address::find($id);
        return view("auth.editAddress")->with("data", $data);
    }
    public function deleteAddress($id) {
        $adr = Address::find($id);
        $adr->delete();
        
        return Redirect::route('profile',2)->with('message', 'Address Removed Succesfully');  
    }
    public function placeOrder(Request $request) {
        $id = $request->adr;
        $adr = Address::find($id);
        $cart = session()->get('cart');
        $products = Product::get();
        $total = 0;
        if($cart) {
            foreach($products as $product) { 
                foreach($cart as $prd) {
                    if($prd['id'] == $product['id']) {
                        $product['cart'] = 1;
                        $product['cartQuantity'] = $prd['quantity'];                   
                        $total = $total + $prd['quantity'] * $product['price'];
                    }
                }
            } 
        }  
        $order = Order::create([
            'userId' => Auth::user()->id,
            'adrId' => $id,
            'total' => $total,
            'status' => 'pending'
        ]);
        foreach($products as $prd) {
            if($prd['cart'] == 1) {
                OrderProduct::create([
                    'orderId' => $order->id, 
                    'productId' => $prd['id'],
                    'quantity' => $prd['cartQuantity']
                ]);
            }
        }
        $request->session()->forget('cart');
        return Redirect::route('profile')->with('message', 'Order Placed Succesfully'); 
    }
    public function cancelOrder($id) {
        DB::table("orders")
            ->where("orders.id","=", $id)
            ->where("orders.orderPlaced", true)
            ->update(["status"=> 'cancelled']);
        
        return Redirect::route('profile')->with('message', 'Order Cancellled Succesfully'); 
    }
    public function review(Request $request) {
        $data = $request->all();
        Review::create([
            'prdId' => $data['prdId'],
            'userId' => Auth::user()->id,
            'desc' => $data['desc'],
            'rate' => $data['rate']
        ]);
        return Redirect::route('profile')->with('message', 'Reviewd Succesfully'); 
    }
    public function invoice($id) {
        $order = DB::table('orders')
            ->join('users', 'orders.userId', 'users.id') 
            ->join('addresses', 'orders.adrId', 'addresses.id') 
            ->select('orders.id','orders.total','orders.status','orders.created_at','orders.updated_at','addresses.address','addresses.phoneNumber')   
            ->where('orders.id','=',$id)   
            ->get();   
        $orderProducts = DB::table("order_products")
            ->join('products','order_products.productId','=','products.id')
            ->select('order_products.*','products.price','products.name','products.image','products.desc')   
            ->where('order_products.orderId','=',$id)              
            ->get();  
 
        return view('auth.invoice')->with('orders',$order)->with("orderProducts", $orderProducts);
    }
}
