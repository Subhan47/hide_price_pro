<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulesVariants extends Model
{
    use HasFactory;
    protected $fillable = ['rule_id', 'variant_id','rule_applied_to'];

    protected $hidden = ['created_at', 'updated_at'];

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }


    public function scopeWithEnabledRule($query, $variantId)
    {
        return $query->where(['variant_id' => $variantId, 'rule_applied_to' => 'variants'])
            ->with(['rule' => function ($query) {
                $query->where('is_enabled', true);
            }]);
    }

    public function scopeWithEnabledRules2($query, $variantIds)
    {
        return $query->whereIn('variant_id', $variantIds)
            ->with(['rule' => function ($query) {
                $query->where('is_enabled', true);
            }]);
    }


    public function scopeWithEnabledRuleAndCategory($query, $type)
    {
        return $query->where('rule_applied_to', $type)
            ->whereHas('rule', function ($query) {
                $query->where('is_enabled', true);
            });
    }

}
