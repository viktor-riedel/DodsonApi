<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\YardResource;
use App\Models\Yard;
use Illuminate\Http\Request;

class YardsController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->collectYards();
    }

    public function create(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        Yard::create([
            'yard_name' => $request->input('name'),
            'location_country' => $request->input('location_country'),
            'address' => $request->input('address'),
            'approx_shipping_days' => $request->input('approx_shipping_days'),
        ]);
        return $this->collectYards();
    }

    public function delete(Yard $yard): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $yard->delete();
        return $this->collectYards();
    }

    public function update(Request $request, Yard $yard): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $yard->update([
            'yard_name' => $request->input('name'),
            'location_country' => $request->input('location_country'),
            'address' => $request->input('address'),
            'approx_shipping_days' => $request->input('approx_shipping_days'),
        ]);
        return $this->collectYards();
    }

    private function collectYards(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $yards = Yard::get();
        return YardResource::collection($yards);
    }
}
