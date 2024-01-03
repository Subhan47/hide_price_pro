<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

trait ValidateRequestTrait
{
    /**
     * Validate the given request with the provided rules.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateRuleStoreRequest(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'max:255',
            'variant_id' => 'required|array',
        ];

        return Validator::make($request->all(), $rules);
    }

}
