<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
     *
     * @throws \RuntimeException
     */
    public function index(Request $request): JsonResponse
    {
        $params = [
            'auth'  => [
                $request->get('login', null),
                $request->get('password', null),
            ],
            'debug' => true
        ];

        if ($includeParams = $request->get('include')) {
            $params = [
                'query' => [
                    'include' => $includeParams
                ]
            ];
        }

        $response = $this->client->get('/api/v2/project/filter', $params);

        $responseBody = [];

        if ($response->getStatusCode() === 200) {
            $response = json_decode(
                (string) $response->getBody()->getContents(),
                true
            );

            $responseBody['data'] = $response['data'] ?? [];
        }

        return response()->json([
            'code'   => $response->getStatusCode(),
            'data'   => $responseBody,
        ]);
    }
}
