<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\IngredientShortage as IngredientShortageEmail;

class OrderTest extends TestCase
{

    private $orderPath = '/api/v1/orders';

    /**
     * @test
     */
    public function order_Added_successfully_and_stock_correctly_updated()
    {
        $data = [
            'products' => [
                [
                    'product_id' => Product::first()->id,
                    'quantity' => rand(1, 5)
                ], [
                    'product_id' => Product::skip(1)->first()->id,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];

        $this->post($this->orderPath, $data)->assertCreated();
        /** Order has been added*/
        $this->assertEquals(1, Order::count());
        $this->assertDatabaseCount('order_product', sizeof($data['products']));
        /******************************** */

        $order = Order::first();

        $ingredients = [];
        foreach ($data['products'] as $product) {
            /** Order Details have been added */
            $this->assertDatabaseHas('order_product', array_merge($product, ['order_id'    => $order->id]));
            /******************************** */

            // Calculate total requested grams for each ingredient in product in an order
            $productIngredients = Product::find($product['product_id'])->ingredients;
            foreach ($productIngredients as $ingredient) {
                $ingredients[$ingredient->id] = array_key_exists($ingredient->id, $ingredients) ?
                    $ingredients[$ingredient->id] + ($ingredient->pivot->amount * $product['quantity']) :
                    $ingredient->pivot->amount * $product['quantity'];
            }
        }

        foreach ($ingredients as $id => $newAmount) {
            /** Ingrediants have been updated  */
            $this->assertDatabaseHas('ingredients', [
                'id' => $id,
                'current_amount' => (Ingredient::find($id)->max_amount) - $newAmount
            ]);
        }
    }

    /**
     * @test
     */
    public function reject_order_if_not_enough_ingredients_in_stock()
    {
        $product = Product::first();
        $product->ingredients->first()->update(['current_amount' => 0]);

        $data = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(0, Order::count());
    }

    /**
     * @test
     */
    public function product_id_required_in_order_request()
    {
        $data = [
            'products' => [
                [
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnprocessable();
    }

    /**
     * @test
     */
    public function quantity_required_in_order_request()
    {
        $product = Product::first();
        $data = [
            'products' => [
                [
                    'product_id' => $product->id,
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnprocessable();
    }

    /**
     * @test
     */
    public function product_id_in_order_request_must_exist_in_order_table()
    {
        $productsCount = Product::count();
        $data = [
            'products' => [
                [
                    'product_id' => $productsCount + 1,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnprocessable();
    }
}
