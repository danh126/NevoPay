<?php

namespace tests\Unit\Models;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_code_is_uuid()
    {
        $transaction = Transaction::factory()->create();
        $this->assertTrue(Str::isUuid($transaction->transaction_code));
    }

    public function test_transaction_has_sender_and_receiver_relations()
    {
        $transaction = Transaction::factory()->create();

        $this->assertNotNull($transaction->senderWallet);
        $this->assertNotNull($transaction->receiverWallet);
    }
}
