<?php

namespace App\Http\Controllers\Directories\ContrAgents;

use App\Http\Controllers\Controller;
use App\Http\Requests\DIrectories\ContrAgent\ContrAgentRequest;
use App\Http\Resources\ContrAgents\ContrAgentResource;
use App\Models\ContrAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ContrAgentsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ContrAgentResource::collection($this->getAgentsList());
    }

    public function get(ContrAgent $contrAgent): ContrAgentResource
    {
        return new ContrAgentResource($contrAgent);
    }

    public function create(ContrAgentRequest $request): \Illuminate\Http\JsonResponse
    {
        $contrAgent = ContrAgent::create([
            'name' => ucfirst(trim($request->input('name'))),
            'person_name' => ucfirst(trim($request->input('person_name'))),
            'country' => strtoupper(trim($request->input('country'))),
            'email' => strtolower(trim($request->input('email'))),
            'phone' => trim($request->input('phone')),
            'fax' => trim($request->input('fax')),
            'address' => trim($request->input('address')),
        ]);

        return response()->json(['id' => $contrAgent->id], 201);
    }

    public function delete(ContrAgent $contrAgent): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $contrAgent->delete();
        return ContrAgentResource::collection($this->getAgentsList());
    }

    public function update(ContrAgentRequest $request, ContrAgent $contrAgent): \Illuminate\Http\JsonResponse
    {
        $contrAgent->update([
            'name' => ucfirst(trim($request->input('name'))),
            'person_name' => ucfirst(trim($request->input('person_name'))),
            'country' => strtoupper(trim($request->input('country'))),
            'email' => strtolower(trim($request->input('email'))),
            'phone' => trim($request->input('phone')),
            'fax' => trim($request->input('fax')),
            'address' => trim($request->input('address')),
        ]);
        return response()->json(['id' => $contrAgent->id], 202);
    }

    private function getAgentsList(): Collection
    {
        return ContrAgent::orderBy('created_at')->get();
    }
}
