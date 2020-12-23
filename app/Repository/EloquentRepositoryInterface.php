<?php


namespace App\Repository;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

interface EloquentRepositoryInterface
{
    public function create(array $attributes): Model;

    public function find($id): ?Model;

    public function with(array $relationships);

    public function get($where = []): Collection;

    public function paginate_collection(Request $request): LengthAwarePaginator;
}
