<?php

namespace App\Http\Controllers\CRM\Leads;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\Leads\LeadRequest;
use App\Http\Resources\CRM\Leads\LeadResource;
use App\Models\Lead;
use App\Models\User;

class LeadsController extends Controller
{
    public function list(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $leads = Lead::with('acceptedBy')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

        return LeadResource::collection($leads);
    }

    public function lead(Lead $lead): \Illuminate\Http\JsonResponse
    {
        return response()->json($lead);
    }

    public function newLeadData(): \Illuminate\Http\JsonResponse
    {
        $status = [];
        foreach(Lead::LEAD_STATUS as $key => $value) {
            $status[] = [
              'type' => $key,
              'value' => $value,
            ];
        }
        $types = [];
        foreach(Lead::LEAD_TYPES as $key => $value) {
            $types[] = [
                'type' => $key,
                'value' => $value,
            ];
        }
        $users = User::all()->filter(function($user) {
            return $user->hasAllRoles(['ADMIN']);
        })->values();
        return response()->json([
            'status' => $status,
            'types' => $types,
            'users' => $users,
        ]);
    }

    public function create(LeadRequest $request): \Illuminate\Http\JsonResponse
    {
        $leadId = Lead::create([
            'lead_name' => $request->validated('lead_name'),
            'from' => $request->validated('lead_name'),
            'lead_type' => $request->validated('lead_type'),
            'lead_status' => $request->validated('lead_status'),
            'lead_description' => $request->validated('lead_description'),
            'accepted_by' =>$request->validated('accepted_by'),
        ]);

        return response()->json(['id' => $leadId], 201);
    }

    public function update(LeadRequest $request, Lead $lead): \Illuminate\Http\JsonResponse
    {
        $lead->update([
            'lead_name' => $request->validated('lead_name'),
            'from' => $request->validated('lead_name'),
            'lead_type' => $request->validated('lead_type'),
            'lead_status' => $request->validated('lead_status'),
            'lead_description' => $request->validated('lead_description'),
            'accepted_by' => $request->input('accepted_by'),
        ]);
        return response()->json(['accepted' => true], 202);
    }
}
