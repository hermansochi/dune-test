<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChangePrice;

class TestDivergeCheck extends Controller
{

    /**
     * 
     * Inject custom request validator
     * 
     * @param App\Http\Requests\ChangePrice $request
     * @return void
     * 
     */
    public function __construct(ChangePrice $request)
    {
        //
    }
    /**
     * Check resource. Fake store.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request->input();
    }

}
