<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\Request;

interface Importable
{
    public function importResources();

    public function importEntity(Request $request);
}
