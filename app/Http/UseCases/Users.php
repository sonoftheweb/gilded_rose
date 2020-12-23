<?php


namespace App\Http\UseCases;


use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Users extends BaseUseCase
{
    /**
     * @param array $definition
     * @param $id
     * @param $request
     * @return Model|null
     */
    public function defaultItem(array $definition, $id, $request): ?Model
    {
        $id = ($id === 'me') ? auth()->guard('api')->user()->id : $id;
        $eloquentInterface = new BaseRepository($definition['model']);
        $eloquentInterface = $eloquentInterface->with($this->relations);
        return $eloquentInterface->find($id);
    }
}
