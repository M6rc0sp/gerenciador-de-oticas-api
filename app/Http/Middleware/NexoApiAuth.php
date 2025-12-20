<?php

namespace App\Http\Middleware;

use App\Jobs\SendAutoRegistrationCredentialsEmail;
use App\Models\Store;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class NexoApiAuth
{
    /**
     * Handle an incoming request.
     *
     * Valida o token Bearer JWT do Nexo e extrai o store_id.
     * Anexa store_id e store ao request para uso nos controllers.
     * Se a store existir mas o usuário não, cria o usuário e retorna 402 com credenciais.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token de autenticação não fornecido',
                'error' => 'unauthorized',
            ], 401);
        }

        $token = substr($authHeader, 7); // Remove "Bearer "

        try {
            // Decodificar payload do JWT (sem validar assinatura por enquanto)
            // Em produção, você deve validar a assinatura com a chave do Nexo
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                throw new \Exception('Token JWT inválido');
            }

            $payload = json_decode(
                base64_decode(strtr($parts[1], '-_', '+/')),
                true
            );

            if (! $payload) {
                throw new \Exception('Payload do token inválido');
            }

            // Extrair store_id do payload (pode vir como storeId, store_id, ou iss)
            $storeId = $payload['storeId'] ?? $payload['store_id'] ?? $payload['iss'] ?? null;

            if (! $storeId) {
                return response()->json([
                    'message' => 'Store ID não encontrado no token',
                    'error' => 'invalid_token',
                ], 401);
            }

            // Buscar store no banco de dados pelo nuvemshop_id
            $store = Store::where('nuvemshop_id', $storeId)->first();

            // Se store não existe, criar automaticamente
            if (! $store) {
                // Extrair email do payload se disponível, caso contrário gerar um
                $storeEmail = $payload['email'] ?? $payload['store_email'] ?? $payload['user_email'] ?? "store-{$storeId}@nexo.generated";

                $store = Store::create([
                    'nuvemshop_id' => $storeId,
                    'access_token' => 'nexo-generated-'.Str::random(32),
                    'name' => $payload['name'] ?? $payload['store_name'] ?? "Loja {$storeId}",
                    'email' => $storeEmail,
                    'is_active' => true,
                ]);

                \Log::info("Store criada automaticamente: {$store->nuvemshop_id} ({$store->email})");
            }

            if (! $store->is_active) {
                return response()->json([
                    'message' => 'Loja desativada. Entre em contato com o suporte.',
                    'error' => 'store_inactive',
                ], 403);
            }

            // Verificar se usuário já existe pela store
            $user = User::where('email', $store->email)->first();

            if (! $user) {
                // Criar novo usuário com password aleatória
                $generatedPassword = Str::random(16);
                $user = User::create([
                    'email' => $store->email,
                    'name' => $store->name ?? explode('@', $store->email)[0],
                    'password' => Hash::make($generatedPassword),
                ]);

                // Despachar job para enviar email com credenciais
                SendAutoRegistrationCredentialsEmail::dispatch($user, $generatedPassword);

                // Retornar 402 Payment Required (status de "ação necessária") com credenciais
                // O frontend deve capturar isso e exibir tela de registro com auto-login
                return response()->json([
                    'message' => 'Usuário criado com sucesso. Use as credenciais abaixo para fazer login.',
                    'status' => 'user_created',
                    'credentials' => [
                        'email' => $store->email,
                        'password' => $generatedPassword,
                        'name' => $user->name,
                    ],
                    'instructions' => 'Você recebeu um email com suas credenciais. Use-as para fazer login.',
                ], 402);
            }

            // Anexar dados ao request para uso nos controllers
            $request->merge([
                'auth_store_id' => $store->nuvemshop_id,
                'auth_store' => $store,
                'auth_user' => $user,
            ]);

            // Também disponibilizar via attributes para acesso mais direto
            $request->attributes->set('store', $store);
            $request->attributes->set('store_id', $store->nuvemshop_id);
            $request->attributes->set('user', $user);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token de autenticação inválido',
                'error' => 'invalid_token',
                'details' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
