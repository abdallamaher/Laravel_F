<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['status'];

    // Order Statuses
    public const STATUS_PENDING = 0;
    public const STATUS_ONGOING = 1;
    public const STATUS_COMPLETED = 2;
    public static function getTypes()
    {
        return collect([
            self::STATUS_PENDING => 'pending',
            self::STATUS_ONGOING => 'ongoing',
            self::STATUS_COMPLETED => 'completed',
        ]);
    }

    /**
     * Get Products associated with the Order
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withPivot('quantity');
    }
}
