<?php


namespace App\Repository\Eloquent;


use App\Repository\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class BaseRepository implements EloquentRepositoryInterface
{

    protected $query;

    public function __construct(Model $model)
    {
        $this->query = $model::query();
    }

    public function create(array $attributes): Model
    {
        return $this->query->create($attributes);
    }

    public function find($id): ?Model
    {
        return $this->query->findOrFail($id);
    }

    public function with(array $relationships): BaseRepository
    {
        $this->query = $this->query->with($relationships);
        return $this;
    }

    public function get($where = []): Collection
    {
        foreach ($where as $key => $w) {
            $this->query = $this->query->where($key, $w);
        }

        return $this->query->get();
    }

    public function paginate_collection(Request $request): LengthAwarePaginator
    {
        $perPage = (isset($request->pagination_count)) ? $request->pagination_count : 10;
        return $this->query->paginate($perPage);
    }
}
