<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class Order extends Model
{
    use HasFactory;

    // Exact enum values from database schema
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cart_items',
        'total_price',
        'status',
        'user_id',
        'user',
        'delivery',
        'payment',
        'file',
        'shipping_address',
        'message'
    ];

    protected $casts = [
        'cart_items' => 'array',
        'delivery' => 'collection',
        'payment' => 'collection',
        'user' => 'collection',
        'total_price' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            // Calculate total price if not set or if cart items have changed
            if (empty($order->total_price) || !empty($order->cart_items)) {
                $order->total_price = collect($order->cart_items)
                    ->reduce(function ($total, $item) {
                        return $total + 
                            (floatval($item['price'] ?? 0) * 
                             intval($item['quantity'] ?? 1));
                    }, 0);
            }

            // Validate and set status
            $order->status = $order->validateStatus($order->status);
        });

        static::created(function ($order) {
            $recipient = User::where('id', 1)->first();

            Notification::make()
                ->title("Новый заказ №$order->id")
                ->body("Стоимость заказа $order->total_price")
                ->success()
                ->send()
                ->sendToDatabase($recipient);
        });
    }

    // Dedicated method for status validation
    public function validateStatus($status): string
    {
        // Normalize status to lowercase
        $normalizedStatus = strtolower(trim((string)$status));

        // Check if status is in the allowed list
        if (in_array($normalizedStatus, self::STATUS)) {
            return $normalizedStatus;
        }

        // Default to pending if invalid
        return self::STATUS_PENDING;
    }

    // Mutator to ensure status is always valid
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $this->validateStatus($value);
    }

    public function getTotalPriceAttribute($value)
    {
        // If no value is set, calculate from cart items
        if (empty($value) && !empty($this->cart_items)) {
            return collect($this->cart_items)
                ->reduce(function ($total, $item) {
                    return $total + 
                        (floatval($item['price'] ?? 0) * 
                         intval($item['quantity'] ?? 1));
                }, 0);
        }
        
        return $value;
    }

    public function setTotalPriceAttribute($value)
    {
        // If value is not set or is zero, calculate from cart items
        if (empty($value) && !empty($this->cart_items)) {
            $this->attributes['total_price'] = collect($this->cart_items)
                ->reduce(function ($total, $item) {
                    return $total + 
                        (floatval($item['price'] ?? 0) * 
                         intval($item['quantity'] ?? 1));
                }, 0);
        } else {
            $this->attributes['total_price'] = $value;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
