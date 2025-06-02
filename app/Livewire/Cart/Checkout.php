<?php

namespace App\Livewire\Cart;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
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
use Illuminate\Support\Collection;

class Checkout extends Component
{
    use WithFileUploads;
    public $name;
    public $email;
    public $phone;
    public $comment;
    public $company_name;
    public $inn;
    public $bik;
    public $correspondent_account;
    public $bank_account;
    public $yur_address;
    public $file;
    public $type = "natural";
    public $products = [];
    public $constructs = [];
    public $kpp;
    public $legal_address;
    public $message;
    public $payment_methods;
    public $selected_payment_method;
    public $delivery_address;

    public $deliveries;
    public $selected_delivery;
    protected $listeners = ["cartUpdated" => "handleCartUpdate"];

    protected $messages = [
        "name.required" => "Пожалуйста, введите ваше имя",
        "email.required" => "Пожалуйста, введите email",
        "email.email" => "Пожалуйста, введите корректный email",
        "phone.required" => "Пожалуйста, введите номер телефона",
        "company_name.required" => "Пожалуйста, введите название компании",
        "inn.required" => "Пожалуйста, введите ИНН",
        "bik.required" => "Пожалуйста, введите БИК",
        "correspondent_account.required" =>
            "Пожалуйста, введите корреспондентский счет",
        "bank_account.required" => "Пожалуйста, введите банковский счет",
        "yur_address.required" => "Пожалуйста, введите юридический адрес",
        "file.required" => "Пожалуйста, прикрепите файл с реквизитами",
        "file.mimes" => "Разрешенные типы файлов (pdf,doc,docx,xls,xlsx,csv)",
        "file.max" => "Максимальный размер файла 2 МБ",
    ];

    public function rules()
    {
        $rules["natural"] = [
            "name" => "required|string|max:255",
            "email" => "required|email|max:255",
            "phone" => "required|string|max:20",
        ];

        $rules["legal"] = [
            "name" => "required|string|max:255",
            "email" => "required|email|max:255",
            "phone" => "required|string|max:20",
            "company_name" => "required|string|max:255",
            "inn" => "required|string|max:255",
            "bik" => "required|string|max:255",
            "correspondent_account" => "required|string|max:255",
            "bank_account" => "required|string|max:255",
            "yur_address" => "required|string|max:255",
            "file" => "nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv|max:2048",
        ];

        return $rules[$this->type];
    }

    public function updatedSelectedDelivery()
    {
        // dd($this->deliveries[$this->selected_delivery - 1]);
        $this->initMap($this->deliveries[$this->selected_delivery - 1]);
    }

    public function mount()
    {
        $this->products = [];
        $this->constructs = [];
        $this->name = Auth::user() ? Auth::user()->name : "";
        $this->type = Auth::user() ? Auth::user()->type : "natural";
        $this->email = Auth::user() ? Auth::user()->email : "";
        $this->phone = Auth::user() ? Auth::user()->phone : "";
        $this->company_name = Auth::user() ? Auth::user()->company_name : "";
        $this->inn = Auth::user() ? Auth::user()->inn : "";
        $this->kpp = Auth::user() ? Auth::user()->kpp : "";
        $this->bik = Auth::user() ? Auth::user()->bik : "";
        $this->correspondent_account = Auth::user()
            ? Auth::user()->correspondent_account
            : "";
        $this->bank_account = Auth::user() ? Auth::user()->bank_account : "";
        $this->yur_address = Auth::user() ? Auth::user()->yur_address : "";

        $this->payment_methods = PaymentMethod::where("is_active", true)->get();
        if ($this->payment_methods->isNotEmpty()) {
            $this->selected_payment_method = $this->payment_methods->first()->id;
        }

        $this->deliveries = Delivery::where("is_active", true)->get();
        if ($this->deliveries->isNotEmpty()) {
            $this->selected_delivery = $this->deliveries->first()->id;
        }
    }

    public function render()
    {
        return view("livewire.cart.checkout", [
            "payment_methods" => $this->payment_methods,
            "deliveries" => $this->deliveries,
        ]);
    }

    public function initMap($delivery)
    {
        if ($delivery->type == "map") {
            if ($delivery->points) {
                $this->dispatch("init-map", delivery: $delivery);
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
        $this->products = new Collection();

        if (empty($cartItems)) {
            return $this->products;
        }

        $productIds = [];

        foreach ($cartItems as $key => $item) {
            if ($item == null) {
                continue;
            }
            $productIds[] = $key;
        }

        $this->products = ProductVariant::whereIn("id", $productIds)->get();

        return $this->products;
    }

    public function loadConstructs($constructItems = [])
    {
        $this->constructs = collect([]);
        if (empty($constructItems)) {
            return $this->constructs;
        }
        foreach ($constructItems as $item) {
            if ($item == null) {
                continue;
            }
            $product = ProductVariant::where("id", $item["id"])->first();
            $small_box = ProductVariant::where(
                "id",
                $item["boxes"]["small"]["id"]
            )->first();
            $medium_box = ProductVariant::where(
                "id",
                $item["boxes"]["medium"]["id"]
            )->first();
            $large_box = ProductVariant::where(
                "id",
                $item["boxes"]["large"]["id"]
            )->first();

            if (!$product || !$small_box || !$medium_box || !$large_box) {
                continue;
            }

            $item["product"] = $product;
            $item["boxes"]["small"]["product"] = $small_box;
            $item["boxes"]["medium"]["product"] = $medium_box;
            $item["boxes"]["large"]["product"] = $large_box;

            $price =
                $product->price +
                $small_box->price * $item["boxes"]["small"]["count"] +
                $medium_box->price * $item["boxes"]["medium"]["count"] +
                $large_box->price * $item["boxes"]["large"]["count"];

            $item["price"] = $price;

            $this->constructs->put($item["id"], $item);
        }

        return $this->constructs;
    }

    public function placeOrder($products)
    {
        // Validate input
        $this->validate();

        if (Auth::id()) {
            $user = User::where("id", Auth::id())->first();
        } else {
            // Find or create user
            $user = User::where("email", $this->email)->firstOr(function () {
                $password = Str::random(10);
                $new_user = User::create([
                    "name" => $this->name,
                    "password" => Hash::make($password),
                    "email" => $this->email,
                    "phone" => $this->phone,
                ]);

                $new_user->notify(new WelcomeNotification($password));

                return $new_user;
            });

            // Login the user
            Auth::login($user);
        }

        if ($this->type === "legal") {
            $user->update([
                "type" => $this->type,
                "company_name" => $this->company_name,
                "inn" => $this->inn,
                "kpp" => $this->kpp,
                "bik" => $this->bik,
                "correspondent_account" => $this->correspondent_account,
                "bank_account" => $this->bank_account,
                "yur_address" => $this->yur_address,
            ]);
        }

        // Calculate total price
        $totalPrice = 0;
        foreach ($products as $product) {
            $price = $product["new_price"] ?? $product["price"];
            $totalPrice += $price * $product["quantity"];
        }

        // Create order
        $order = Order::create([
            "user_id" => $user->id,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "cart_items" => $products,
            "total_price" => $totalPrice,
            "user" => $user,
            "shipping_address" => $this->delivery_address,
            "delivery" => Delivery::find($this->selected_delivery),
            "payment" => PaymentMethod::find($this->selected_payment_method),
            "file" => $this->file ? $this->file->store(path: "orders") : null,
            "message" => $this->comment,
            "status" => "pending",
        ]);

        // После создания заказа отправляем уведомление
        $user->notify(new OrderCreatedNotification($order));

        // Clear cart
        session()->forget("cart");

        // Dispatch cart cleared event
        $this->dispatch("cart-cleared");

        // Redirect to order success page
        return redirect()->route("client.thanks")->with("order_id", $order->id);
    }

    public function checkCompany()
    {
        $token = env("DADATA_TOKEN");
        $dadata = new \Dadata\DadataClient($token, null);
        $result = $dadata->findById("party", $this->inn, 1);

        if ($result) {
            $this->message = null;
            $this->company_name = $result[0]["value"];
            $this->kpp = isset($result[0]["data"]["kpp"])
                ? $result[0]["data"]["kpp"]
                : null;
            $this->yur_address = $result[0]["data"]["address"]["value"];
        } else {
            $this->message =
                "Мы не смогли найти компанию по ИНН. Введите другой ИНН или заполните данные вручную";
        }
    }
}
