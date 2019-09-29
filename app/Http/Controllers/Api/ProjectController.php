<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

use const true, null;

use function json_decode;

/**
 * Class ProjectController
 *
 * @package App\Http\Controllers\Api
 */
class ProjectController extends Controller
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ProjectController constructor.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => Config::get('proxy.base_url'),
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'auth'  => [
                $request->get('login', null),
                $request->get('password', null),
            ],
        ];

        if ($includeParams = $request->get('include')) {
            $params = [
                'query' => [
                    'include' => $includeParams
                ]
            ];
        }

        $responseBody = [];

        try {
            $response = $this->client->get('/api/v2/project/filter', $params);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $responseData = json_decode(
                    (string) $response->getBody()->getContents(),
                    true
                );

                $responseBody = [
                    'code' => $response->getStatusCode(),
                    'data' => $responseData['data'] ?? [],
                ];
            }
        } catch (Exception $e) {
            $responseBody = [
                'code'  => Response::HTTP_BAD_REQUEST,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($responseBody);
    }
}
