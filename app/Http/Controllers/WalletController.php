<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Services\WalletService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * GET /wallets
     */
    public function index()
    {
        return response()->json([
            'data' => $this->walletService->getAll(),
        ]);
    }

    /**
     * POST /wallets
     */
    public function store(WalletRequest $request)
    {
        $wallet = $this->walletService->createForUser(
            $request->validated()['user_id'],
            $request->validated()
        );

        return response()->json([
            'message' => 'Created successfully.',
            'data'    => $wallet,
        ], 201);
    }

    /**
     * GET /wallets/{id}
     */
    public function show($id)
    {
        try {
            return response()->json([
                'data' => $this->walletService->getWallet($id),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }
    }

    /**
     * GET /users/{user}/wallet
     */
    public function showByUser($userId)
    {
        try {
            return response()->json([
                'data' => $this->walletService->getByUserId($userId),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'User wallet not found.'], 404);
        }
    }

    /**
     * PUT/PATCH /wallets/{id}
     */
    public function update(WalletRequest $request, $id)
    {
        try {
            $wallet = $this->walletService->update($id, $request->validated());

            return response()->json([
                'message' => 'Updated successfully.',
                'data' => $wallet,
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }
    }

    /**
     * PATCH /wallets/{wallet}/active
     */
    public function updateActive(WalletRequest $request, $id)
    {
        try {
            $wallet = $this->walletService->toggleActive(
                $id,
                $request->validated()['is_active']
            );

            return response()->json([
                'message' => 'Updated successfully.',
                'data' => $wallet,
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }
    }

    /**
     * DELETE /wallets/{id}
     */
    public function destroy($id)
    {
        try {
            $this->walletService->delete($id);

            return response()->noContent();
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Wallet not found.'], 404);
        }
    }
}
