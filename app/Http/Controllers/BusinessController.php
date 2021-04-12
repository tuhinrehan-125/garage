<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Currency;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }
    public function saveSettingsAndLocation(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'business_id' => 'required',
                    'currency_id' => 'required',
                    'business_locations.*.name'    => 'required|unique:business_locations,name',
                ],
                [
                    'currency_id.required' => 'Currency is required',
                    'business_locations.*.name.required' => 'Business name is required',
                    'business_locations.*.name.unique' => 'Business location name is already taken'
                ]
            );
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            if(!empty($request['currency_id']))
            {
                $business = Business::findOrFail($request->business_id);
                $business->currency_id = $request->currency_id;
                $business->save();
            }

            if(!empty($request['business_locations']))
            {
                foreach ($request->business_locations as $location) {
                    Business::addLocation($location, $request->business_id);
                }
            }

            return response()->json(['success' => true, 'data' => $business], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
    }

     /**
     * currency list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllCurrency(Request $request)
    {
        $cur=Currency::all();
        return response()->json(['success' => true, 'data' => $cur], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business)
    {
        //
    }
}
