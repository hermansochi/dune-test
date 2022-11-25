<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChangePrice;
use Illuminate\Http\Request;

class TestDivergeCheck extends Controller
{
    /**
     * Inject custom request validator
     *
     * @param  ChangePrice  $request
     * @return void
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
