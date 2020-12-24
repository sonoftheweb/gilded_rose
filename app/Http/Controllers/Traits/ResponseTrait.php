<?php


namespace App\Http\Controllers\Traits;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Throwable;

trait ResponseTrait
{
    public $statusCode = 200;

    /**
     * Returns the status code as required
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Responds with JSON data
     *
     * @param $data
     * @return JsonResponse
     */
    public function respond($data): JsonResponse
    {
        return Response::json($data,$this->getStatusCode(), [], JSON_INVALID_UTF8_IGNORE);
    }

    /**
     * Responds with error messages
     *
     * @param Throwable $throwable
     * @return JsonResponse
     */
    public function respondWithError(Throwable $throwable): JsonResponse
    {
        $this->setStatusCode($throwable->getCode());
        if (
            $this->statusCode > 700 ||
            $this->statusCode == 0 ||
            $this->statusCode == 42 ||
            $this->statusCode == 22 ||
            $this->statusCode == -1
        ) {
            // Likely an SQL error (for instance: foreign key dependency, not respecting default value...), let's tell them this is not authorized
            $this->setStatusCode(401);
        }

        // make sure devs can track this error by reporting it into logs
        report($throwable);

        $response = [
            'displayAlert' => 'error',
            'message' => 'There was an error while processing your action.',
            'error' => [
                'status_code' => $this->getStatusCode()
            ]
        ];

        if (
            is_a($throwable, 'Symfony\Component\HttpKernel\Exception\HttpException') &&
            array_key_exists('explicit_message', $throwable->getHeaders()) && $throwable->getHeaders()['explicit_message']
        )
            $response['message'] = $throwable->getMessage();

        // we may also do a check to see if the app is in debug mode to show the actual error.
        if (env('APP_DEBUG')) {
            $response = array_merge($response, [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString()
            ]);
        }

        return $this->respond($response);
    }

    /**
     * @param $statusCode
     * @param $response
     * @return JsonResponse
     */
    public function respondWithSuccessMessage($statusCode, $response): JsonResponse
    {
        switch (gettype($response)) {
            case "array":
                return $this->setStatusCode($statusCode)->respond(array_merge([
                    'displayAlert' => 'success'
                ], $response));
            default:
                return $this->setStatusCode($statusCode)->respond([
                    'displayAlert' => 'success',
                    'message' => $response,
                ]);
        }
    }
}
