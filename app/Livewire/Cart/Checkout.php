<?php

namespace App\Livewire\Cart;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Checkout extends Component
{
    public $name;
    public $email;
    public $phone;
    public $products = [];
    protected $listeners = ['cartUpdated' => 'handleCartUpdate'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20'
    ];

    protected $messages = [
        'name.required' => 'Пожалуйста, введите ваше имя',
        'email.required' => 'Пожалуйста, введите email',
        'email.email' => 'Пожалуйста, введите корректный email',
        'phone.required' => 'Пожалуйста, введите номер телефона'
    ];

    public function mount()
    {
        $this->products = [];
    }

    public function render()
    {
        return view('livewire.cart.checkout');
    }

    public function handleCartUpdate($data = null)
    {
        if (!$data || !isset($data['products'])) {
            $this->loadProducts([]);
            return;
        }
        $this->loadProducts($data['products']);
    }

    public function loadProducts($cartItems = [])
    {
        $this->products = [];
        
        if (empty($cartItems)) {
            return $this->products;
        }

        foreach ($cartItems as $cartItem) {
            if (!$cartItem) continue;

            $product = Product::with(['variants', 'img'])->find($cartItem['id']);
            if ($product) {
                $productArray = $product->toArray();
                $productArray['quantity'] = $cartItem['count'];
                
                // Add variations data
                if (isset($cartItem['variations'])) {
                    $productArray['cart_variations'] = $cartItem['variations'];
                }
                
                $this->products[] = $productArray;
            }
        }

        return $this->products;
    }

    public function placeOrder()
    {
        // Validate input
        $this->validate();

        // Find or create user
        $user = User::where('email', $this->email)->firstOr(function () {
            $password = Str::random(10);
            $new_user = User::create([
                'name' => $this->name,
                'password' => Hash::make($password),
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            $new_user->notify(new WelcomeNotification($password));

            return $new_user;
        });

        // Login the user
        Auth::login($user);

        // Calculate total price
        $totalPrice = 0;
        foreach ($this->products as $product) {
            $price = $product['new_price'] ?? $product['price'];
            $totalPrice += $price * $product['quantity'];
        }

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cart_items' => $this->products,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        // Clear cart
        session()->forget('cart');

        // Dispatch cart cleared event
        $this->dispatch('cart-cleared');

        // Redirect to order success page
        return redirect()->route('client.thanks')->with('order_id', $order->id);
    }
}
