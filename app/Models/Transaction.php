<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'date',
        'description'
    ];

    public $timestamps = false;

    public function journal_entries() {
        return $this->hasMany(JournalEntry::class, 'transaction_id', 'transaction_id');
    }
}
