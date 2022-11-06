<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Termwind\Components\Dd;

class ProductController extends Controller
{
    public function index()
    {
        $products =  Product::all();
        return view('welcome', compact('products'));
    }
    //add cart
    public function addCart($id)
    {
        $total_quantity = session()->get('total_quantity');
        if ($total_quantity) {
            session()->put('total_quantity', $total_quantity + 1);
        } else {
            session()->put('total_quantity', 1);
        }
        $product = Product::find($id);
        if (!$product) {
            abort(404);
        }
        $cart = session()->get('cart');
        // if cart is empty then this the first product
        if (!$cart) {
            $cart = [
                $id => [
                    "id" => $product->id,
                    "title" => $product->title,
                    "quantity" => 1,
                    "price" => $product->price,
                    "photo" => $product->image,
                    "total_price" => $product->price
                ]
            ];
            $total_quantity++;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        }
        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
            $total_quantity++;
            $cart[$id]['total_price'] = $cart[$id]['quantity'] * $cart[$id]['price'];
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        }
        // if item not exist in cart then add to cart with quantity = 1
        $cart[$id] = [
            "id" => $product->id,
            "title" => $product->title,
            "quantity" => 1,
            "price" => $product->price,
            "photo" => $product->image,
            "total_price" => $product->price
        ];
        $total_quantity++;
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
    //cart
    public function cart()
    {
        $cart = session()->get('cart');
        $total_net_price = 0;
        if (session()->has('cart')) {
            foreach (session()->get('cart') as $item) {
                $total_net_price += $item['total_price'];
            }
        }
        return view('dashboards.cart', compact('cart', 'total_net_price'));
    }
    //remove cart
    public function deleteCart($id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            $total_quantity = session()->get('total_quantity');
            session()->put('total_quantity', $total_quantity - 1);
        }
        return redirect()->back()->with('success', 'Product removed successfully');
    }
}
