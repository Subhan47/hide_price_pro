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

    public function scopeWithEnabledRule($query, $variantId)
    {
        return $query->where('variant_id', $variantId)
            ->with(['rule' => function ($query) {
                $query->where('is_enabled', true);
            }]);
    }

}
