<?php

namespace App\Http\Controllers;

use App\Services\NuvemshopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NuvemshopController extends Controller
{
    protected NuvemshopService $nuvemshopService;

    public function __construct(NuvemshopService $nuvemshopService)
    {
        $this->nuvemshopService = $nuvemshopService;
    }

    /**
     * Handle app installation from Nuvemshop
     */
    public function install(Request $request): JsonResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json([
                'success' => false,
                'message' => 'Código de autorização é obrigatório',
            ], 400);
        }

        try {
            $result = $this->nuvemshopService->authorize($code);

            if (! $result['success']) {
                return response()->json($result, $result['status'] ?? 400);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro durante instalação: '.$e->getMessage(),
            ], 500);
        }
    }
}
