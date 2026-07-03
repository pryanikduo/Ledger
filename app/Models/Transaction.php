<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'date',
        'description'
    ];

    public $timestamps = false;

    public function journal_entries(): HasMany {
        return $this->hasMany(JournalEntry::class, 'transaction_id', 'transaction_id');
    }
}
