<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'organization_id', 'author_id'];

    public function organization ()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function initialBalance ()
    {
        return $this->hasOne(InitialBalance::class, 'branch_id', 'id');
    }

    public function user ()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function transactions ()
    {
        return $this->hasMany(Transaction::class, 'branch_id', 'id');
    }

    public function dailyBalances()
    {
        return $this->hasMany(DailyBalance::class, 'branch_id', 'id');
    }
}
