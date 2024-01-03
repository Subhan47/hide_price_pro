<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\RulesVariants;
use App\Traits\ValidateRequestTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RuleController extends Controller
{
    use ValidateRequestTrait;
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $rules = Rule::with('variants')->get();
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];

        $rules = $this->paginate($rules, 2);
//        if(@request()->get('page')){
//            return view('partials.rules', compact('rules', 'products'));
//        }

        return view('rules.index', compact('rules', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];
        $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
        return view('rules.create', compact('products','allRuleVariantIDs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = $this->validateRuleStoreRequest($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        $rule = $request->except(['_token', 'variant_id']);
        $rule['is_enabled'] = @$rule['is_enabled'] ? true : false;

        try{
            $rule = Rule::create($rule);
            foreach ($request['variant_id'] as $variantId) {
                RulesVariants::create([
                    'rule_id' => $rule->id,
                    'variant_id' => $variantId,
                ]);
            }
        }
        catch (\Exception $e){
            return response()->json(['errors' => $e->getMessage()], 400);
        }


        return response()->json(['success' => 'Rule Created Successfully'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rule  $rule
     * @return Response
     */
    public function show(Rule $rule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rule  $rule
     * @return Application|Factory|View|Response
     */
    public function edit($id)
    {
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];
        $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
        $rule = Rule::with('variants')->findOrFail($id);
        $ruleVariantIDs = $rule->variants->pluck('variant_id')->flatten()->toArray();
        return view('rules.edit', compact('products','allRuleVariantIDs', 'rule', 'ruleVariantIDs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateRuleStoreRequest($request);
        if ($validator->fails()) {
            $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
            return response()->json(['errors' => $validator->errors()->all(), 'disabledOptions' => $allRuleVariantIDs], 400);
        }

        $ruleData = $request->except(['_token', 'variant_id']);
        $ruleData['is_enabled'] = @$ruleData['is_enabled'] ? true : false;

        try{
            $rule = Rule::findOrFail($id);
            $rule->update($ruleData);

            $rule->variants()->delete();
            foreach ($request['variant_id'] as $variantId) {
                $rule->variants()->create(['variant_id' => $variantId]);
            }

            $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
        }
        catch (\Exception $e){
            $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
            return response()->json(['errors' => $validator->errors()->all(), 'disabledOptions' => $allRuleVariantIDs], 400);
        }
        return response()->json(['success' => 'Rule Updated Successfully', 'disabledOptions' => $allRuleVariantIDs], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try{
            $rule = Rule::findOrFail($id);
            $rule->delete();
        }
        catch (\Exception $e){
            return response()->json(['errors' => $e->getMessage()], 400);
        }
        return response()->json(['success' => 'Rule Deleted Successfully'], 200);
    }

    public function search(Request $request)
    {
        $rules = Rule::getBySearch($request['search'])->with('variants')->get();
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];
        $rules = $this->paginate($rules, 2);

        return view('partials.rules', compact('rules', 'products'));
    }
}
