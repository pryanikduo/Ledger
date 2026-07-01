<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'account_id';

    protected $casts = [
        'code' => 'int',
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'is_active'
    ];

    public function journal_entries() {
        return $this->hasMany(JournalEntry::class, 'account_id', 'account_id');
    }
}
