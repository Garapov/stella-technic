<?php

namespace App\Livewire\Cart;

use App\Models\Delivery;
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
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class Checkout extends Component
{
    use WithFileUploads;
    #[Validate('required|string|max:255')]
    public $name;
    #[Validate('required|email|max:255')]
    public $email;
    #[Validate('required|string|max:20')]
    public $phone;
    #[Validate('nullable|string')]
    public $comment;
    #[Validate('required|string|max:255')]
    public $company_name;
    #[Validate('required|string|max:255')]
    public $inn;
    #[Validate('required|string|max:255')]
    public $bik;
    #[Validate('required|string|max:255')]
    public $correspondent_account;
    #[Validate('required|string|max:255')]
    public $bank_account;
    #[Validate('required|string|max:255')]
    public $yur_address;
    #[Validate('nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv|max:2048')]
    public $file;
    public $type = 'natural';
    public $products = [];
    public $kpp;
    public $legal_address;
    public $message;
    public $payment_methods;
    public $selected_payment_method;
    public $delivery_address;

    public $deliveries;
    public $selected_delivery;
    protected $listeners = ['cartUpdated' => 'handleCartUpdate'];

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
        'file.required' => 'Пожалуйста, прикрепите файл с реквизитами',
        'file.mimes' => 'Разрешенные типы файлов (pdf,doc,docx,xls,xlsx,csv)',
        'file.max' => 'Максимальный размер файла 2 МБ',
    ];

    public function updatedSelectedDelivery() {
        // dd($this->deliveries[$this->selected_delivery - 1]);
        $this->initMap($this->deliveries[$this->selected_delivery - 1]);
    }

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

        $this->deliveries = Delivery::where('is_active', true)->get();
        if ($this->deliveries->isNotEmpty()) {
            $this->selected_delivery = $this->deliveries->first()->id;
           
        };
    }

    public function render()
    {
        return view('livewire.cart.checkout', [ 
            'payment_methods' => $this->payment_methods,
            'deliveries' => $this->deliveries,
        ]);
    }

    public function initMap($delivery) {
        if ($delivery->type == 'map') {
            if ($delivery->points) {
                $this->dispatch('init-map', delivery: $delivery);
            }
        }
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

        $this->initMap($this->deliveries->first());

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
            'user' => $user,
            'shipping_address' => $this->delivery_address,
            'delivery' => Delivery::find($this->selected_delivery),
            'payment' => PaymentMethod::find($this->selected_payment_method),
            'file' => $this->file->store(path: 'orders'),
            'message' => $this->comment,
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
