<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';
    protected $primaryKey = 'journal_id';

    public $timestamps = false;

    protected $casts = [
        'transaction_id' => 'int',
        'account_id' => 'int',
        'amount' => 'float'
    ];

    protected $fillable = [
        'transaction_id',
        'account_id',
        'amount',
        'type'
    ];

    public function account() {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }
}
