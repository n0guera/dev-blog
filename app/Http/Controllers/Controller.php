<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected ?Request $request = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
