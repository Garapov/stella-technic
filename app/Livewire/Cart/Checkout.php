<?php

namespace App\Livewire\Cart;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Checkout extends Component
{
    public $name;
    public $email;
    public $phone;
    public $comment;
    public $type = 'natural';
    public $products = [];
    public $company_name;
    public $inn;
    public $kpp;
    public $bik;
    public $correspondent_account;
    public $bank_account;
    public $yur_address;
    public $message;
    public $payment_methods;
    public $selected_payment_method;
    protected $listeners = ['cartUpdated' => 'handleCartUpdate'];

    public function rules() {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'comment' => 'nullable|string',
        ];

        if ($this->type == 'legal') {
            $rules['company_name'] = 'required|string|max:255';
            $rules['inn'] = 'required|string|max:255';
            // $rules['kpp'] = 'required|string|max:255';
            $rules['bik'] = 'required|string|max:255';
            $rules['correspondent_account'] = 'required|string|max:255';
            $rules['bank_account'] = 'required|string|max:255';
            $rules['yur_address'] = 'required|string|max:255';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Пожалуйста, введите ваше имя',
        'email.required' => 'Пожалуйста, введите email',
        'email.email' => 'Пожалуйста, введите корректный email',
        'phone.required' => 'Пожалуйста, введите номер телефона',
        'company_name.required' => 'Пожалуйста, введите название компании',
        'inn.required' => 'Пожалуйста, введите ИНН',
        // 'kpp.required' => 'Пожалуйста, введите КПП',
        'bik.required' => 'Пожалуйста, введите БИК',
        'correspondent_account.required' => 'Пожалуйста, введите корреспондентский счет',
        'bank_account.required' => 'Пожалуйста, введите банковский счет',
        'yur_address.required' => 'Пожалуйста, введите юридический адрес',
    ];

    public function mount()
    {
        $this->products = [];
        $this->name = Auth::user() ? Auth::user()->name : '';
        $this->type = Auth::user() ? Auth::user()->type : 'natural';
        $this->email = Auth::user() ? Auth::user()->email : '';
        $this->phone = Auth::user() ? Auth::user()->phone : '';
        $this->company_name = Auth::user() ? Auth::user()->company_name : '';
        $this->inn = Auth::user() ? Auth::user()->inn : '';
        $this->kpp = Auth::user() ? Auth::user()->kpp : '';
        $this->bik = Auth::user() ? Auth::user()->bik : '';
        $this->correspondent_account = Auth::user() ? Auth::user()->correspondent_account : '';
        $this->bank_account = Auth::user() ? Auth::user()->bank_account : '';
        $this->yur_address = Auth::user() ? Auth::user()->yur_address : '';

        $this->payment_methods = PaymentMethod::where('is_active', true)->get();
        if ($this->payment_methods->isNotEmpty()) $this->selected_payment_method = $this->payment_methods->first()->id;
        
    }

    public function render()
    {
        return view('livewire.cart.checkout', [ 
            'payment_methods' => $this->payment_methods
        ]);
    }

    public function updatedType()
    {
        $this->message = null;
    }

    public function handleCartUpdate($products)
    {
        $this->loadProducts($products);
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

        if (Auth::id()) {
            $user = User::where('id', Auth::id())->first();
            
        } else {
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
        }

        if ($this->type === 'legal') {
            $user->update([
                'type' => $this->type,
                'company_name' => $this->company_name,
                'inn' => $this->inn,
                'kpp' => $this->kpp,
                'bik' => $this->bik,
                'correspondent_account' => $this->correspondent_account,
                'bank_account' => $this->bank_account,
                'yur_address' => $this->yur_address,
            ]);
        }

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

        // После создания заказа отправляем уведомление
        $order->user->notify(new OrderCreatedNotification($order));

        // Clear cart
        session()->forget('cart');

        // Dispatch cart cleared event
        $this->dispatch('cart-cleared');

        // Redirect to order success page
        return redirect()->route('client.thanks')->with('order_id', $order->id);
    }

    public function checkCompany()
    {
        $token = env('DADATA_TOKEN');
        $dadata = new \Dadata\DadataClient($token, null);
        $result = $dadata->findById("party", $this->inn, 1);

        if ($result) {
            $this->message = null;
            $this->company_name = $result[0]['value'];
            $this->kpp = isset($result[0]['data']['kpp']) ? $result[0]['data']['kpp'] : null;
            $this->yur_address = $result[0]['data']['address']['value'];
        } else {
            $this->message = 'Мы не смогли найти компанию по ИНН. Введите другой ИНН или заполните данные вручную';
        }
    }
}
