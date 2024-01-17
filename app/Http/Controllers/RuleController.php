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
        $rules = Rule::with('variants')->orderByDesc('id')->get();
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];

        $rules = $this->paginate($rules, 2);
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
                $collection = Auth::user()->api()->rest('GET', "admin/api/2023-10/collections/$variantId.json");
                $product = Auth::user()->api()->rest('GET', "admin/api/2023-10/products/$variantId.json");

                // It means $variantId is CollectionID
                if (isset($collection['body']['collection'])) {
                    RulesVariants::create(['rule_id' => $rule->id, 'variant_id' => $variantId]);
                    $collectionProducts = Auth::user()->api()->rest('GET', "admin/api/2023-10/collections/$variantId/products.json");
                    if (isset($collectionProducts['body']['products'])) {
                        foreach($collectionProducts['body']['products'] as $collectionProduct){
                            RulesVariants::updateOrCreate(
                                ['variant_id' => $collectionProduct['id']],
                                ['rule_id' => $rule->id, 'variant_id' => $collectionProduct['id']]
                            );
                            $productDetails = Auth::user()->api()->rest('GET', "admin/api/2023-10/products/$collectionProduct->id.json");
                            foreach($productDetails['body']['product']['variants'] as $productVariant){
                                if($productVariant['title'] !== 'Default Title'){
                                    RulesVariants::updateOrCreate(
                                        ['variant_id' => $productVariant['id']],
                                        ['rule_id' => $rule->id, 'variant_id' => $productVariant['id']]
                                    );
                                }
                            }
                        }
                    }
                }

                // It means $variantId is ProductID
                elseif (isset($product['body']['product'])) {
                    RulesVariants::create(['rule_id' => $rule->id, 'variant_id' => $variantId]);
                    foreach($product['body']['product']['variants'] as $productVariant){
                        if($productVariant['title'] !== 'Default Title'){
                            RulesVariants::updateOrCreate(
                                ['variant_id' => $productVariant['id']],
                                ['rule_id' => $rule->id, 'variant_id' => $productVariant['id']]
                            );
                        }
                    }
                }

                // It means $variantId is Actually VariantID
                else{
                    RulesVariants::create(['rule_id' => $rule->id, 'variant_id' => $variantId,]);
                }
            }
            // Delete if any rule exists but not in RuleVariants table
            Rule::doesntHave('variants')->delete();
        }
        catch (\Exception $e){
            return response()->json(['errors' => $e->getMessage()], 400);
        }
        return response()->json(['success' => 'Rule Created Successfully'], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeBackup(Request $request)
    {
        $validator = $this->validateRuleStoreRequest($request);
        if ($validator->fails()) {
           // $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        $rule = $request->except(['_token', 'variant_id']);
        $rule['is_enabled'] = @$rule['is_enabled'] ? true : false;

        try{
            $rule = Rule::create($rule);
            foreach ($request['variant_id'] as $variantId) {
                $product = Auth::user()->api()->rest('GET', "admin/api/2023-10/products/$variantId.json");
                // It means $variantId is ProductID
                if (isset($product['body']['product'])) {
                    foreach($product['body']['product']['variants'] as $productVariant){
                        if ($existingRecord = RulesVariants::where('variant_id', $productVariant['id'])->first()) {
                            Rule::where('id', $existingRecord['rule_id'])->delete();
                        }
                        RulesVariants::create(['rule_id' => $rule->id, 'variant_id' => $productVariant['id']]);
                    }
                }
                // It means $variantId might be collectionID or VariantID
                else{
                    RulesVariants::create([
                        'rule_id' => $rule->id,
                        'variant_id' => $variantId,
                    ]);
                }

            }
            //$allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
        }
        catch (\Exception $e){
           //$allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
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
        $rules = Rule::getBySearch($request['search'])->with('variants')->orderByDesc('id')->get();
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];
        $rules = $this->paginate($rules, 2);

        return view('partials.rules', compact('rules', 'products'));
    }


    /**
     * Purpose of this method is to check whether the variant_id exists in DB or Not
     * If exists check whether its rule is enabled or not
     * @param Request $request
     * @return JsonResponse
     */
    public function findVariantInDB(Request $request): JsonResponse
    {
        $variantIds = $request->input('variant_ids');
        $ruleVariants = RulesVariants::withEnabledRules($variantIds)->get();
        $result = [];
        foreach ($variantIds as $variantId) {
            $associatedRule = $ruleVariants->where('variant_id', $variantId)->first();
            if (@$associatedRule && @$associatedRule->rule) {
                $result[] = [
                    'variant_id' => $variantId,
                    'rule_exists_enabled' => true,
                ];
            }
            else {
                $result[] = [
                    'variant_id' => $variantId,
                    'rule_exists_enabled' => null,
                ];
            }
        }
        return response()->json($result);
    }


    public function categoryData(Request $request): JsonResponse
    {
        $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];
        $products = collect($products)->reject(function ($product) use ($allRuleVariantIDs) {
            return in_array($product['id'], $allRuleVariantIDs);
        });

        if($request['category'] == 'products'){
            return response()->json(['data' => $products->values()->all()]);
        }
        elseif ($request['category'] == 'variants')
        {
            $productVariants = $products->flatMap(function ($product) {
                return collect($product['variants'])
                    ->reject(function ($variant) {
                        return $variant['title'] === 'Default Title';
                    })
                    ->map(function ($variant) use ($product) {
                        return [
                            'id' => $variant['id'],
                            'title' => @$product['title'] . ' - ' .  @$variant['title'] . ' - ' . @$variant['price']
                        ];
                    });
            })
                ->filter(function ($productVariant) use ($allRuleVariantIDs) {
                    return !in_array($productVariant['id'], $allRuleVariantIDs);
                });

            return response()->json(['data' => $productVariants->values()->all()]);
        }

        else{
            $shopCollects = Auth::user()->api()->rest('GET', 'admin/api/2023-10/collects.json');
            $collects = $shopCollects['body']['collects'];
            $collections = collect($collects)->unique('collection_id')->reject(function ($collect) use ($allRuleVariantIDs) {
                return in_array($collect['collection_id'], $allRuleVariantIDs);
            })->pluck('collection_id')->toArray();
            $collectionsDetails = [];
            foreach ($collections as $collection){
                $collectionDetails = Auth::user()->api()->rest('GET', "admin/api/2023-10/collections/$collection.json");
                $collectionsDetails[] = [
                    'id' => $collectionDetails['body']['collection']['id'],
                    'title' => $collectionDetails['body']['collection']['title'],
                ];
            }
            return response()->json(['data' => $collectionsDetails]);
        }
    }


    public function categoryDataBackup(Request $request): JsonResponse
    {
        $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();
        $shop = Auth::user()->api()->rest('GET', 'admin/api/2023-10/products.json');
        $products = $shop['body']['products'];
        $products = collect($products)->reject(function ($product) use ($allRuleVariantIDs) {
            return in_array($product['id'], $allRuleVariantIDs);
        });

        if($request['category'] == 'products'){
            return response()->json(['data' => $products->values()->all()]);
        }
        elseif ($request['category'] == 'variants')
        {
            $productVariants = $products->flatMap(function ($product) {
                return collect($product['variants'])
                    ->reject(function ($variant) {
                        return $variant['title'] === 'Default Title';
                    })
                    ->map(function ($variant) use ($product) {
                        return [
                            'id' => $variant['id'],
                            'title' => @$product['title'] . ' - ' .  @$variant['title'] . ' - ' . @$variant['price']
                        ];
                    });
            })
                ->filter(function ($productVariant) use ($allRuleVariantIDs) {
                    return !in_array($productVariant['id'], $allRuleVariantIDs);
                });

            return response()->json(['data' => $productVariants->values()->all()]);
        }

        else{
            return response()->json(['data' => 'Its collection']);
        }
    }
    public function categoryDataBackup2(Request $request): JsonResponse
    {
        $allRuleVariantIDs = RulesVariants::pluck('variant_id')->toArray();

        // Collection
        $shopCollects = Auth::user()->api()->rest('GET', 'admin/api/2023-10/collects.json');
        $collects = $shopCollects['body']['collects'];
        $collections = collect($collects)->unique('collection_id')->reject(function ($collect) use ($allRuleVariantIDs) {
            return in_array($collect['collection_id'], $allRuleVariantIDs);
        })->pluck('collection_id')->toArray();
        $collectionsDetails = [];
        foreach ($collections as $collection){
            $collectionDetails = Auth::user()->api()->rest('GET', "admin/api/2023-10/collections/$collection.json");
            $collectionsDetails[] = [
                'id' => $collectionDetails['body']['collection']['id'],
                'title' => $collectionDetails['body']['collection']['title'],
            ];
        }
        // retrieving products of the collections
        $products = [];
        foreach ($collectionsDetails as $collection){
            $collectionProducts = Auth::user()->api()->rest('GET', "admin/api/2023-10/collections/{$collection['id']}/products.json");
            $products = $collectionProducts['body']['products'];
            $products = collect($products)->reject(function ($product) use ($allRuleVariantIDs) {
                return in_array($product['id'], $allRuleVariantIDs);
            });
            $products[] = $products->values()->all();
        }

        if($request['category'] == 'collections'){
            return response()->json(['data' => $collectionsDetails]);
        }
        elseif($request['category'] == 'products'){
            return response()->json(['data' => $products]);
        }
        else
        {
            // retrieving variants of the products
            $products = collect($products);
            $productVariants = $products->flatMap(function ($product) {
                return collect($product['variants'])
                    ->reject(function ($variant) {
                        return $variant['title'] === 'Default Title';
                    })
                    ->map(function ($variant) use ($product) {
                        return [
                            'id' => $variant['id'],
                            'title' => @$product['title'] . ' - ' .  @$variant['title'] . ' - ' . @$variant['price']
                        ];
                    });
            })->filter(function ($productVariant) use ($allRuleVariantIDs) {
                    return !in_array($productVariant['id'], $allRuleVariantIDs);
            });

            return response()->json(['data' => $productVariants->values()->all()]);
        }
    }

}
