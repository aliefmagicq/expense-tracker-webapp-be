<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'author_id'];

    public function author ()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function branches ()
    {
        return $this->hasMany(Branch::class, 'organization_id', 'id');
    }
}
