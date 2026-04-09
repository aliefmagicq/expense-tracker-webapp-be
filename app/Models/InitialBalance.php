<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialBalance extends Model
{
    /** @use HasFactory<\Database\Factories\InitialBalanceFactory> */
    use HasFactory;

    protected $fillable = ['amount', 'notes', 'branch_id'];

    public function branch ()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function initialBalances ()
    {
        return $this->hasMany(InitialBalance::class, 'initial_balance_id', 'id');
    }
}
