<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'title', 'description','is_enabled'];

    public function variants()
    {
        return $this->hasMany(RulesVariants::class, 'rule_id');
    }

    public function variantIDs()
    {
        return $this->variants()->pluck('variant_id')->flatten()->toArray();
    }


    public function scopeGetBySearch($query,$search)
    {
        return $query->where(function ($query) use ($search) {
            $query->orWhere('id', $search)
                ->orWhere('title', 'like', '%' . $search . '%')
                ->orWhereRaw("IF(is_enabled, 'enabled', 'disabled') LIKE ?", ['%' . $search . '%']);

        });
    }
}
