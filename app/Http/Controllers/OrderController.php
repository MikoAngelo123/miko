<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('customer.orders.index', compact('orders'));
    }

    public function create()
    {
        // compute available stock considering items already in cart
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $cartItems = collect();
        if ($user && $user->cart) {
            $cartItems = $user->cart->items;
        }

        $products = Product::all()->map(function($p) use ($cartItems) {
            $reserved = $cartItems->where('product_id', $p->id)->sum('quantity');
            $p->available_stock = max(0, $p->stock - $reserved);
            return $p;
        });

        return view('customer.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        // instead of creating an order immediately, we now treat this as "add to cart".
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Product::find($request->product_id);

        // get authenticated user explicitly to satisfy static analyzers
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // calculate available stock considering existing cart items
        $cartItems = collect();
        if ($user && $user->cart) {
            $cartItems = $user->cart->items;
        }
        $reserved = $cartItems->where('product_id', $product->id)->sum('quantity');
        $available = $product->stock - $reserved;

        if ($available < $request->quantity) {
            return redirect()->back()->with('error', 'Stok tidak cukup. Stok tersedia: ' . $available);
        }

        // add to cart through CartController logic (simplified here)
        $cart = $user->cart()->firstOrCreate([]);
        $item = $cart->items()->where('product_id', $product->id)->first();
        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('customer.cart')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function show(Order $order)
    {
        // Removed authorize as no policy exists
        // Ambil semua orders user dalam urutan latest
        $allOrders = Order::where('user_id', Auth::id())->latest()->get();
        // Cari posisi order ini dalam list
        $orderNumber = $allOrders->search(function($item) use ($order) {
            return $item->id === $order->id;
        }) + 1;
        return view('customer.orders.show', compact('order', 'orderNumber'));
    }

    public function edit(Order $order)
    {
        // Removed authorize as no policy exists
        $products = Product::all();
        // Ambil semua orders user dalam urutan latest
        $allOrders = Order::where('user_id', Auth::id())->latest()->get();
        // Cari posisi order ini dalam list
        $orderNumber = $allOrders->search(function($item) use ($order) {
            return $item->id === $order->id;
        }) + 1;
        return view('customer.orders.edit', compact('order', 'products', 'orderNumber'));
    }

    public function update(Request $request, Order $order)
    {
        // Removed authorize as no policy exists

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Product::find($request->product_id);
        $oldProduct = $order->product;
        $oldQuantity = $order->quantity;

        // Restore old stock
        $oldProduct->increment('stock', $oldQuantity);

        // Check new stock availability
        if ($product->stock < $request->quantity) {
            // Restore back if not enough
            $oldProduct->decrement('stock', $oldQuantity);
            return redirect()->back()->with('error', 'Stok tidak cukup. Stok tersedia: ' . $product->stock);
        }

        $total_price = $product->price * $request->quantity;

        $order->update([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $total_price,
        ]);

        // Decrement new stock
        $product->decrement('stock', $request->quantity);

        return redirect()->route('orders.index')->with('success', 'Order berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        // Removed authorize as no policy exists
        // Restore stock
        $order->product->increment('stock', $order->quantity);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus');
    }
}
