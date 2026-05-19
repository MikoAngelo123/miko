<?php

namespace App\Http\Controllers;

use App\Models\Product;

use App\Models\CartItem;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class CustomerController extends Controller
{
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $totalOrders = $user->orders()->count();
        $totalPaid = $user->orders()->sum('total_price');
        $totalProducts = Product::count();
        return view('customer.dashboard', compact('totalOrders', 'totalPaid', 'totalProducts'));
    }

    public function products()
    {
        $products = Product::all();
        return view('customer.products', compact('products'));
    }

    public function cart(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $cart = $user->cart()->firstOrCreate([]);
        $items = $cart->items()->with('product')->get();
        return view('customer.cart', compact('items'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        $cart = $user->cart()->firstOrCreate([]);
        $cartItem = $cart->items()->where('product_id', $request->product_id)->first();
        if ($cartItem) {
            $cartItem->update(['quantity' => $cartItem->quantity + $request->quantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('customer.cart')->with('success', 'Product added to cart');
    }

    public function updateCartItem(Request $request, CartItem $item)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($item->cart->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($item->product->stock < $request->quantity) {
            return redirect()->route('customer.cart')->with('error', 'Insufficient stock for ' . $item->product->title);
        }

        $item->update(['quantity' => $request->quantity]);

        return redirect()->route('customer.cart')->with('success', 'Cart updated successfully.');
    }

    public function removeCartItem(Request $request, CartItem $item)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($item->cart->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();

        return redirect()->route('customer.cart')->with('success', 'Item removed from cart.');
    }

    public function checkout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty');
        }

        // This initial check is good for a quick failure before starting a transaction.
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('customer.cart')->with('error', 'Insufficient stock for ' . $item->product->title);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($cart->items as $item) {
                // Lock the product row to prevent race conditions on stock.
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if ($product->stock < $item->quantity) {
                    throw new \Exception('Insufficient stock for ' . $product->title);
                }

                Order::create([
                    'user_id' => $user->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'total_price' => $product->price * $item->quantity,
                    'status' => 'pending',
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->route('customer.cart')->with('error', 'An error occurred during checkout. Please try again.');
        }

        return redirect()->route('customer.orders')->with('success', 'Order placed successfully');
    }

    public function orders(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // The view `customer.orders` expects a variable named `$orders`.
        // Sort by oldest to ensure sequential numbering in the view.
        $orders = $user->orders()->with('product')->oldest()->get();
        return view('customer.orders', compact('orders'));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('customer.orders')->with('error', 'Only pending orders can be canceled.');
        }

        // Restore stock
        $order->product->increment('stock', $order->quantity);

        $order->delete();

        return redirect()->route('customer.orders')->with('success', 'Order deleted successfully.');
    }

    public function showSettings()
    {
        return view('customer.settings');
    }

    public function updateSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(3)],
        ]);

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('customer.settings')->with('success', 'Account updated successfully.');
    }
}
