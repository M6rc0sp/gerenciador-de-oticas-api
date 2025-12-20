<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NuvemshopService
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $tokenUrl = 'https://www.nuvemshop.com.br/apps/authorize/token';

    protected string $apiBaseUrl = 'https://api.nuvemshop.com.br/v1';

    public function __construct()
    {
        $this->clientId = config('services.nuvemshop.client_id');
        $this->clientSecret = config('services.nuvemshop.client_secret');
    }

    /**
     * Authorize app installation with Nuvemshop
     */
    public function authorize(string $code): array
    {
        try {
            Log::info('Tentando autorizar com código: '.substr($code, 0, 10).'...');

            $response = Http::asForm()->post($this->tokenUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);

            if (! $response->successful()) {
                Log::error('Erro na autorização Nuvemshop', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Falha na autorização: '.$response->body(),
                    'status' => $response->status(),
                ];
            }

            $data = $response->json();

            if (! isset($data['access_token'])) {
                Log::error('Token não recebido na resposta', $data);

                return [
                    'success' => false,
                    'message' => 'Falha na autorização: Token não recebido',
                    'status' => 400,
                ];
            }

            Log::info('Token recebido com sucesso: '.substr($data['access_token'], 0, 10).'...');

            // Salvar informações da loja
            $this->saveStoreInfo($data);

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Erro na autorização Nuvemshop: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Erro interno: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Save store information from OAuth response
     */
    protected function saveStoreInfo(array $data): void
    {
        $storeId = $data['user_id'];
        $accessToken = $data['access_token'];

        // Buscar informações adicionais da loja
        $storeInfo = $this->getStoreInfo($storeId, $accessToken);

        Store::updateOrCreate(
            ['nuvemshop_id' => $storeId],
            [
                'access_token' => $accessToken,
                'name' => $storeInfo['name']['pt'] ?? $storeInfo['name']['es'] ?? 'Loja',
                'email' => $storeInfo['email'] ?? null,
                'domain' => $storeInfo['main_domain'] ?? null,
                'original_domain' => $storeInfo['original_domain'] ?? null,
                'plan' => $storeInfo['plan_name'] ?? null,
                'country' => $storeInfo['country'] ?? 'BR',
                'currency' => $storeInfo['currency'] ?? 'BRL',
                'is_active' => true,
            ]
        );

        Log::info("Loja {$storeId} salva/atualizada com sucesso");
    }

    /**
     * Get store info from Nuvemshop API
     */
    protected function getStoreInfo(int $storeId, string $accessToken): array
    {
        try {
            $response = Http::withHeaders([
                'Authentication' => "bearer {$accessToken}",
                'User-Agent' => config('services.nuvemshop.user_agent', 'Gerenciador Oticas (suporte@example.com)'),
            ])->get("{$this->apiBaseUrl}/{$storeId}/store");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Não foi possível obter informações da loja', [
                'store_id' => $storeId,
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Erro ao obter informações da loja: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Make authenticated API request to Nuvemshop
     */
    public function apiRequest(int $storeId, string $endpoint, string $method = 'GET', array $data = []): array
    {
        $store = Store::where('nuvemshop_id', $storeId)->first();

        if (! $store) {
            return [
                'success' => false,
                'message' => 'Loja não encontrada',
            ];
        }

        try {
            $request = Http::withHeaders([
                'Authentication' => "bearer {$store->access_token}",
                'User-Agent' => config('services.nuvemshop.user_agent', 'Gerenciador Oticas (suporte@example.com)'),
                'Content-Type' => 'application/json',
            ]);

            $url = "{$this->apiBaseUrl}/{$storeId}/{$endpoint}";

            $response = match (strtoupper($method)) {
                'POST' => $request->post($url, $data),
                'PUT' => $request->put($url, $data),
                'DELETE' => $request->delete($url),
                default => $request->get($url),
            };

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
