<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CardController extends Controller
{
    /**
     * Card Add method
     */
    public function cartAdd(Request $request)
    {
		$device_id = $request->get('device_id');
		$cart = Cart::where('device_id', $device_id)->where('cart_status','pending')->first();
		
		$product_id = $request->get('id');
	
 
		$product_qnty = $request->get('product_qnty');
		$product_pcs = $request->get('product_pcs');
	 
		
		$product_info = Product::where('id',$product_id)->first(); 
		$final_sale_price = $product_info->final_sale_price;
		$purchase_price = $product_info->purchase_price;
		$sale_price = $product_info->sale_price;
		$discount = $product_info->discount;
		$subtotal_price = $product_qnty * $final_sale_price; 
		
        if(empty($cart)){
			$cart = Cart::create([
                    'device_id'=>$request->get('device_id'), 
                    'total_purchase_price'=>0, 
                    'total_sale_price'=>0, 
                    'total_discount'=>0, 
                    'total_final_price'=>0, 
                    'total_profit'=>0, 
                    'cart_status'=>'pending', 
                    'cart_by'=>'user name', 
                    'notes'=>''
                ]);
		}
		
							
		$cartItem = CartItem::where('product_id',$product_id)->
							where('cart_id',$cart->id)->
							where('status','pending')->first(); 
		$cartItemQnty = 0;
		if(empty($cartItem)){
				$cartItem = CartItem::create([ 
					'customer_id'=>$cart->customer_id, 
					'cart_id'=>$cart->id, 
					'product_id'=>$product_id, 
					'purchase_price'=>$purchase_price, 
					'sale_price'=>$sale_price, 
					'discount'=>$discount, 
					'final_sale_price'=>$final_sale_price, 
					'product_qnty'=>$product_qnty, 
					'product_pcs'=>$product_pcs, 
					'subtotal_price'=>$subtotal_price, 
					'status'=>'pending'
			]);
		
			$cartItemQnty = $cartItem->product_qnty;
			$cartItemTotalPurchase = $cartItem->purchase_price*$cartItemQnty;
			$cartItemTotalSale = $cartItem->sale_price*$cartItemQnty;
			$cartItemTotalDiscount = $cartItem->discount*$cartItemQnty;
			$cartItemTotalFinalSale = $cartItem->final_sale_price*$cartItemQnty;
			$cartItemSubTotal = $cartItemTotalFinalSale;
			$cartItemTotalProfit = $cartItemTotalFinalSale-$cartItemTotalPurchase;
			
		}else{
			$cartItemQnty = $product_qnty;
			$cartItemTotalPurchase = $cartItem->purchase_price*$cartItemQnty;
			$cartItemTotalSale = $cartItem->sale_price*$cartItemQnty;
			$cartItemTotalDiscount = $cartItem->discount*$cartItemQnty;
			$cartItemTotalFinalSale = $cartItem->final_sale_price*$cartItemQnty;
			$cartItemSubTotal = $cartItemTotalFinalSale;
			$cartItemTotalProfit = $cartItemTotalFinalSale-$cartItemTotalPurchase;
			 
			$cartItem->product_qnty = $cartItemQnty;
			$cartItem->subtotal_price = $cartItemSubTotal;
			$cartItem->save();  
		}	 
		
	
		$cart->save();

		$cartItems = CartItem::where('cart_id',$cart->id)->get();
		
		$gr_qnty = $cartItems->sum('product_qnty'); 
		$gr_subtotal = $cartItems->sum('subtotal_price'); 
		
		$cart->product_qnty = $gr_qnty;
		$cart->total_final_price = $gr_subtotal;
		$cart->save();
		
		$carts = Cart::where('id',$cart->id)->with('cartItem')->with('cartItem.product')->first();
		return response()->json([
			'status'=>'success',
			'data_type'=>'cart_add',
			'data'=>$carts,
		]);
    }

    /**
     * Card Update Method
     */
    public function cartUpdate(Request $request)
    {
		$device_id = $request->get('device_id');
		$cart_item_id = $request->get('id');
		$product_qnty = $request->get('product_qnty');
		$product_pcs = $request->get('product_pcs');
		
		$cartItem = CartItem::where('id',$cart_item_id)->first();
		if(empty($cartItem)){
			 return response()->json([
			        'status'=>'error',
			        'data_type'=>'cart_update'
		        ],500);
		}
		$cart = Cart::where('id',$cartItem->cart_id)->first();
		
		$product_id = $request->get('product_id');
		$purchase_price = $request->get('purchase_price');
		$sale_price = $request->get('sale_price');
		$discount = $request->get('discount');
		$final_sale_price = $request->get('final_sale_price');
		
		$subtotal_price = $product_qnty * $final_sale_price;
		
        if(empty($cart)){
			 
		}
							
		$cartItem = $cartItem->update([ 
					'product_qnty'=>$product_qnty, 
					'product_pcs'=>$product_pcs, 
					'subtotal_price'=>$subtotal_price
		]);
		
		$cartItems = CartItem::where('cart_id',$cart->id)->get();
		
		$gr_qnty = $cartItems->sum('product_qnty'); 
		$gr_subtotal = $cartItems->sum('subtotal_price'); 
		
		$cart->product_qnty = $gr_qnty;
		$cart->total_final_price = $gr_subtotal;
		$cart->save();
		
		$carts = Cart::where('id',$cart->id)->with('cartItem')->with('cartItem.product')->first();
		return response()->json([
			'status'=>'success',
			'data_type'=>'cart_add',
			'data'=>$carts,
		]);
    }

    /**
     * Card Remove Method
     */
    public function cartRemove($cartitemid=null)
    {
		$cartItem = CartItem::where('id','=',$cartitemid)->first();
		$cart_id = $cartItem->cart_id;
		$cart = Cart::where('id',$cart_id)->first();
		$deleteCartItem = $cartItem->delete();
		if($deleteCartItem){
			$cartItems = CartItem::where('cart_id',$cart->id)->get();
			$gr_qnty = $cartItems->sum('product_qnty'); 
			$gr_subtotal = $cartItems->sum('subtotal_price'); 
			
			$cart->product_qnty = $gr_qnty;
			$cart->total_final_price = $gr_subtotal;
			$cart->save();
			
			$carts = Cart::where('id',$cart->id)->with('cartItem')->with('cartItem.product')->first();
			
			return response()->json([
				'status'=>'success',
				'data_type'=>'cart_remove',
				'data'=>$carts,
			]);
			 
		}else{
			return response()->json([
				'status'=>'error',
				'message'=>'Sorry, product was not deleted!'
			],403);
		}
	}

    /**
     * Card Confirm Method
     */
    public function cartConfirm(Request $request)
    {
		try{
			$device_id = $request->get('device_id');
			$cart_id = $request->get('cart_id');
			$customer_id = $request->get('user_id');
			if($cart_id){
				$cart = Cart::where('id',$cart_id)->first();
			}
		 
			
			
			if(!empty($cart)){
				$cartItem = CartItem::where('cart_id', $cart->id)->get();
			}
			if(!empty($cart->customer_id)){
				$customer_id = $cart->customer_id;
			} 
			
			if(empty($cart)){
				return response()->json([
					'status'=>'error',
					'data'=>"Cart is empty!"
				],403);
			}
			$order = Order::create([
					'customer_id'=>$customer_id, 
					'cart_id'=>$cart->id, 
					'product_qnty'=>$cart->product_qnty, 
					'total_purchase_price'=>$cart->total_purchase_price, 
					'total_sale_price'=>$cart->total_sale_price, 
					'total_discount'=>$cart->total_discount, 
					'total_final_price'=>$cart->total_final_price, 
					'total_profit'=>$cart->total_final_price-$cart->total_purchase_price, 
					'cart_status'=>'processing', 
					'cart_by'=>'User', 
					'notes'=>''
			]);
			foreach($cartItem as $key=>$item){
				OrderItem::create([
					'customer_id'=>$customer_id,
					'order_id'=>$order->id,
					'cart_id'=>$item->cart_id,
					'product_id'=>$item->product_id,
					'purchase_price'=>$item->purchase_price,
					'sale_price'=>$item->sale_price,
					'discount'=>$item->discount,
					'final_sale_price'=>$item->final_sale_price,
					'product_qnty'=>$item->product_qnty,
					'subtotal_price'=>$item->subtotal_price,
					'status'=>'processing',
				]);
			}
			
			$order_items = OrderItem::where(['cart_id'=>$cart->id])->get();
			
			$order_address = OrderAddress::create([
							'customer_id'=>$request->input('customer_id'), 
							'cart_id'=>$cart->id, 
							'order_id'=>$order->id, 
							'house_mess_name'=>$request->input('house_mess_name'), 
							'user_full_name'=>$request->input('user_full_name'), 
							'full_address'=>$request->input('full_address'), 
							'user_mobile'=>$request->input('user_mobile'), 
							'user_type'=>$request->input('user_type'), 
							'branch_name'=>$request->input('branch_name'), 
							'delivery_time'=>$request->input('delivery_time'), 
							'notes'=>'unverified'
			]);
			$updateCart = Cart::where('id',$cart->id)->update(['cart_status'=>'confirmed']);
			$updateCartItem = CartItem::where('cart_id', $cart->id)->update(['status'=>'confirmed']);
			return response()->json([
				'status'=>'success',
				'data_type'=>'cart_confirmed',
				'data'=>$request->all(),
			]);
		}catch(Exception $ex){
			return response()->json([
			'status'=>'error',
			'data'=>$ex->getMessage()
		],403);
		}
    }
	
}
