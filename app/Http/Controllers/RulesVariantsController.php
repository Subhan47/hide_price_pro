<?php

namespace App\Http\Controllers;

use App\Models\RulesVariants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RulesVariantsController extends Controller
{

    /**
     * Retrieve all Rules Variants.
     *
     * @param $type
     * @return JsonResponse
     */
    public function all($type)
    {
        $rulesVariants = RulesVariants::withEnabledRuleAndCategory($type)->get();
        return response()->json($rulesVariants);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RulesVariants  $rulesVariants
     * @return Response
     */
    public function show(RulesVariants $rulesVariants)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RulesVariants  $rulesVariants
     * @return Response
     */
    public function edit(RulesVariants $rulesVariants)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RulesVariants  $rulesVariants
     * @return Response
     */
    public function update(Request $request, RulesVariants $rulesVariants)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RulesVariants  $rulesVariants
     * @return Response
     */
    public function destroy(RulesVariants $rulesVariants)
    {
        //
    }
}
