<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_price');
        $totalProducts = Product::count();
        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalProducts'));
    }

    public function products()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    public function createProduct()
    {
        return view('admin.create-product');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'title' => $request->title,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    public function showProduct(Product $product)
    {
        return view('admin.show-product', compact('product'));
    }

    public function editProduct(Product $product)
    {
        return view('admin.edit-product', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'title' => $request->title,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function orders()
    {
        $orders = Order::with(['user', 'product'])->latest()->get();
        return view('admin.orders', compact('orders'));
    }

    public function completeOrder(Order $order)
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'complete']);
            return redirect()->route('admin.orders')->with('success', 'Order marked as complete.');
        }
        return redirect()->route('admin.orders')->with('error', 'This order is already complete.');
    }

    public function showSettings()
    {
        return view('admin.settings');
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

        return redirect()->route('admin.settings')->with('success', 'Account updated successfully.');
    }
}
