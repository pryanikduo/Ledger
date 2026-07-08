<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;

class AccountController extends Controller
{
    public function balance(Account $account) {
        return response()->json([
            'account_id' => $account->getKey(),
            'name' => $account->name,
            'code' => $account->code,
            'balance' => $account->balance,
        ]);
    }
}
