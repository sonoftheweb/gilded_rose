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
     * @return array
     */
    public function purchase($id, $request): array
    {
        if (auth()->guard('api')->guest()) {
            return [
                'status' => 401,
                'throwable' => new \Exception('You cannot purchase a product unless you are authenticated.', 401)
            ];
        }

        // we are not doing anything fancy. Just reduce the number of products by ID
        Product::query()->where('id', $id)->decrement('items_available');
        return [
            'status' => 200,
            'message' => 'Successful purchased item.'
        ];
    }
}
