<?php


namespace App\Http\UseCases;


use App\Models\Product;

class Products extends BaseUseCase
{
    protected $relations = [
        'images'
    ];

    /**
     * @param $id
     * @param $request
     * @return string
     */
    public function purchase($id, $request): string
    {
        // we are not doing anything fancy. Just reduce the number of products by ID
        Product::query()->where('id', $id)->decrement('items_available');
        return 'Successful purchased item.';
    }
}
