<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => 'Product ' . $this->faker->randomDigit,
            'description' => $this->faker->text(400),
            'price' => $this->faker->randomNumber(5)
        ];
    }

    /**
     * For each model perform some action
     *
     * @return ProductFactory
     */
    public function configure(): ProductFactory
    {
        return $this->afterCreating(function (Product $product) {
            ProductImage::factory()->count($this->faker->randomDigit)->create([
                'product_id' => $product->id
            ]);
        });
    }
}
