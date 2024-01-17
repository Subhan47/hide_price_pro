<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulesVariants extends Model
{
    use HasFactory;
    protected $fillable = ['rule_id', 'variant_id'];

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }

    public function scopeWithEnabledRules($query, $variantIds)
    {
        return $query->whereIn('variant_id', $variantIds)
            ->with(['rule' => function ($query) {
                $query->where('is_enabled', true);
            }]);
    }

}
