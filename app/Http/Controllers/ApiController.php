<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ResponseTrait;

    /**
     * @return \string[][]
     */
    public function getResourceMap(): array
    {
        $mapping = [
            'products' => [
                'model' => new Product,
                'use_case' => '\App\Http\UseCases\Products'
            ],
            'users' => [
                'model' => new User,
                'use_case' => '\App\Http\UseCases\Users'
            ],
        ];

        $endpointBaseUri = request()->route()->getPrefix();
        foreach ($mapping as $key => &$data) {
            // Define endpoint url
            $data['endpoint'] = url('/') . '/' . $endpointBaseUri . '/' . $key;
            // Define resource used for mapping
            $data['resource'] = $key;
        }

        return $mapping;
    }

    /**
     * @param $resource
     * @param null $request
     * @return string[]
     * @throws \ReflectionException
     */
    public function getResource($resource, $request = null): array
    {
        $resourceMap = $this->getResourceMap();
        if (!isset($resourceMap[$resource])) {
            abort(404, 'Resource not found.');
        }

        $resource = $resourceMap[$resource];

        $resource['use_case'] = new $resource['use_case']($request);
        $resource['modelShortName'] = (new \ReflectionClass($resource['model']))->getShortName();

        return $resource;
    }

    /**
     * @param $resource
     * @param Request $request
     * @return JsonResponse
     */
    public function getCollection($resource, Request $request): JsonResponse
    {
        try {
            $resource = $this->getResource($resource, $request);

            // todo, implement resources here
            return $this->respond([
                'data' => $resource['use_case']->smartCollection($resource, $request)
            ]);
        } catch (\Throwable $e) {
            return $this->respondWithError($e);
        }
    }

    /**
     * @param $resource
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getItem($resource, $id, Request $request): JsonResponse
    {
        try {
            if (!isset($id)) {
                throw new \Exception('Please provide a valid id.', 404);
            }

            $resource = $this->getResource($resource, $request);

            return $this->respond([
                'data' => $resource['use_case']->smartItem($resource, $id, $request),
            ]);
        } catch (\Throwable $e) {
            return $this->respondWithError($e);
        }
    }

    /**
     * @param Request $request
     * @param $resource
     * @param $id
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function updateItem(Request $request, $resource, $id): JsonResponse
    {
        $resource = $this->getResource($resource, $request);
        $message = $resource['use_case']->updateItem($resource, $id, $request);

        return $this->respondWithSuccessMessage(202, $message);
    }
}
