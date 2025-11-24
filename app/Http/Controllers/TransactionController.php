<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    /**
     * POST /transactions/deposit
     */
    public function deposit(TransactionRequest $request): JsonResponse
    {
        $userId = Auth::id(); // dùng Laravel Auth
        $result = $this->transactionService->deposit(
            $request->wallet_number,
            $request->amount,
            $userId
        );

        return response()->json([
            'success' => true,
            'message' => 'Deposit request accepted.',
            'data' => $result
        ], 201);
    }

    /**
     * POST /transactions/withdraw
     */
    public function withdraw(TransactionRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $result = $this->transactionService->withdraw(
            $request->wallet_number,
            $request->amount,
            $userId
        );

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request accepted.',
            'data' => $result
        ], 201);
    }

    /**
     * POST /transactions/transfer
     */
    public function transfer(TransactionRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $result = $this->transactionService->transfer(
            $request->wallet_number,
            $request->to_wallet_number,
            $request->amount,
            $userId
        );

        return response()->json([
            'success' => true,
            'message' => 'Transfer request accepted.',
            'data' => $result
        ], 201);
    }
}
