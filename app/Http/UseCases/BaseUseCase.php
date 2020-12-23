<?php


namespace App\Http\UseCases;


use App\Repository\Eloquent\BaseRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseUseCase
{
    protected $request = null;

    protected $definition = null;

    protected $relations = [];

    protected $allowEmptyData = false;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $definition
     * @param Request $request
     * @return mixed
     */
    public function smartCollection(array $definition, Request $request)
    {
        $methodsCheck = $this->getRequestMethods($definition, $request);
        if ($methodsCheck)
            return $methodsCheck;

        return $this->defaultCollection($definition, $request);
    }


    /**
     * Get request functions from parameters
     * Sample request: http://0.0.0.0/api/v1/products?groupBy=status&filter=with-images
     * Response: [
     *   'full' => 'groupByStatusFilterWithImages', // Used if function exists in model.
     *   'groupBy' => [                 // Complementary of filter (OR condition).
     *       0 => 'groupByStatus',      // Used if function exists in model and full isn't.
     *       1 => 'groupBy',            // Used if function exists in model and full + groupByStatus aren't.
     *   ],
     *   'filter' => [                  // Complementary of groupBy (OR condition).
     *       0 => 'filterWithImages',   // Used if function exists in model and full isn't.
     *       1 => 'filter',             // Used if function exists in model and full + filterLikelySpam aren't.
     *   ],
     * ]
     *
     * @return string[]
     */
    private function getRequestFunctions(): array
    {
        $functions = ['full' => ''];
        foreach ($this->request->all() as $key => $value) {
            $functions['full'] .= $this->formatRequestParam($key) . $this->formatRequestParam($value);
            if (!isset($functions[$key])) {
                $functions[$key] = [];
            }
            $functions[$key][] = lcfirst($this->formatRequestParam($key) . $this->formatRequestParam($value));
            $functions[$key][] = lcfirst($this->formatRequestParam($key));
        }
        $functions['full'] = lcfirst($functions['full']);
        return $functions;
    }

    /**
     * Format request params to build a specific function of current use case
     *
     * @param $param
     * @return string|string[]
     */
    private function formatRequestParam($param)
    {
        $param = preg_replace('/[-_]/', ' ', $param);   // hello-world => hello world
        $param = ucwords($param);                       // hello world => Hello World
        $param = str_replace(' ', '', $param);          // Hello World => HelloWorld
        return $param;
    }

    public function defaultCollection(array $definition, Request $request): LengthAwarePaginator
    {
        $eloquentInterface = new BaseRepository($definition['model']);
        $eloquentInterface = $eloquentInterface->with($this->relations);
        return $eloquentInterface->paginate_collection($request);
    }

    /**
     * Smart transform an object using a definition
     *
     * @param array $definition Definition
     * @param $id
     * @param $request
     * @return array
     */
    public function smartItem(array $definition, $id, $request): array
    {
        $methodsCheck = $this->getRequestMethods($definition, $request);
        if ($methodsCheck)
            return $methodsCheck;

        $response['data'] = $this->defaultItem($definition, $id, $request);
        return $response;
    }

    /**
     * @param array $definition
     * @param $id
     * @param $request
     * @return Model|null
     */
    public function defaultItem(array $definition, $id, $request): ?Model
    {
        $eloquentInterface = new BaseRepository($definition['model']);
        $eloquentInterface = $eloquentInterface->with($this->relations);
        return $eloquentInterface->find($id);
    }


    /**
     * @param array $definition
     * @param $id
     * @param $request
     * @return array|Application|Translator|string|null
     */
    public function updateItem(array $definition, $id, $request)
    {
        $methodsCheck = $this->getRequestMethods($definition, $request, $id);
        if ($methodsCheck)
            return $methodsCheck;

        // left this here to show the differences of not using a repository
        // If we were to port this app to a different DB or framework, we would have to modify all
        // eloquent calls as seen here.

        $item = $definition['model']::findOrFail($id);
        if (!empty($item)) {
            foreach ($item->getFillable() as $column) {
                if ($request->input($column) !== null || $this->allowEmptyData) {
                    $item->$column = $request->input($column);
                }
            }
            $item->save();
        }

        return 'Successful updated the resource record.';
    }

    /**
     * @param array $definition
     * @param $request
     * @param null $id
     * @return mixed
     */
    private function getRequestMethods(array $definition, $request, $id = null)
    {
        $this->request = $request;
        $this->definition = $definition;
        $functions = $this->getRequestFunctions();

        foreach ($functions as $function) {
            if (is_string($function) && method_exists($this, $function)) {
                //a response that should contain an array with data (kinda mandatory?)
                return ($id) ? $this->$function($id, $request) : $this->$function($request);
            }

            if (is_array($function)) {
                foreach ($function as $subFunction) {
                    if (!method_exists($this, $subFunction)) {
                        continue;
                    }
                    //a response that should contain an array with data (kinda mandatory?)
                    return ($id) ? $this->$subFunction($id, $request) : $this->$subFunction($request);
                }
            }
        }

        return false;
    }
}
