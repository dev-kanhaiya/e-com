<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Get or create active cart for user.
     */
    private function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    /**
     * Display customer cart.
     */
    public function index(): View
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->items()->with(['product.primaryImage', 'product.vendor'])->get();

        $subtotal = $cart->totalAmount();
        $shipping = $subtotal > 0 ? 50.00 : 0.00; // Flat ₹50 shipping for demo
        $total = $subtotal + $shipping;

        return view('customer.cart', compact('items', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Add product to cart with stock validation.
     */
    public function add(AddToCartRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            return back()->with('error', 'This product is currently unavailable.');
        }

        $cart = $this->getOrCreateCart();
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        $requestedQty = (int) $request->quantity;
        $newQty = $cartItem ? ($cartItem->quantity + $requestedQty) : $requestedQty;

        // Stock check
        if ($product->stock < $newQty) {
            return back()->with('error', "Insufficient stock! Only {$product->stock} items available.");
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'price' => $product->price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $requestedQty,
                'price' => $product->price,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    /**
     * Update cart item quantity with stock validation.
     */
    public function update(Request $request, int $itemId): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getOrCreateCart();
        $cartItem = $cart->items()->where('id', $itemId)->firstOrFail();
        $product = $cartItem->product;

        if ($request->quantity > $product->stock) {
            return back()->with('error', "Insufficient stock! Only {$product->stock} items available.");
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'price' => $product->price, // keep updated to current price in active cart
        ]);

        return back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove individual item from cart.
     */
    public function remove(int $itemId): RedirectResponse
    {
        $cart = $this->getOrCreateCart();
        $cartItem = $cart->items()->where('id', $itemId)->firstOrFail();
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all items in cart.
     */
    public function clear(): RedirectResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();

        return back()->with('success', 'Cart cleared successfully.');
    }
}
