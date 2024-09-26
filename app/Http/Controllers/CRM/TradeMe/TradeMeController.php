<?php

namespace App\Http\Controllers\CRM\TradeMe;

use App\Http\Controllers\Controller;
use App\Http\ExternalApiHelpers\TradeMeApiHelper;
use App\Http\ExternalApiHelpers\TradeMeHelper;
use App\Http\Resources\CRM\TradeMe\TradeMeAuthResource;
use App\Http\Resources\CRM\TradeMe\TradeMeGroupResource;
use App\Http\Resources\CRM\TradeMe\TradeMeTemplateResource;
use App\Models\TradeMeGroup;
use App\Models\TradeMeTemplate;
use App\Models\TradeMeToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TradeMeController extends Controller
{
    public function getAuthData(): TradeMeAuthResource
    {
        $tradeMeAuth = TradeMeToken::with('user')->first();
        return new TradeMeAuthResource($tradeMeAuth);
    }

    public function getVerificationUrl(Request $request): TradeMeAuthResource
    {
        $helper = new TradeMeHelper();
        $tradeMeTokens = $helper->getVerificationUrl($request->user());
        return new TradeMeAuthResource($tradeMeTokens);
    }

    public function deleteAuthorization(): TradeMeAuthResource
    {
        TradeMeToken::first()?->delete();
        $tradeMeAuth = TradeMeToken::with('user')->first();
        return new TradeMeAuthResource($tradeMeAuth);
    }

    public function setAuthorization(Request $request): JsonResponse
    {
        $tradeMe = TradeMeToken::first();
        if ($tradeMe) {
            $tradeMe->update([
                'oauth_token' => $request->input('oauth_token'),
                'oauth_verifier' => $request->input('oauth_verifier'),
            ]);
            $tradeMe->refresh();
            $helper = new TradeMeHelper();
            $helper->setAccessTokens($tradeMe);
        }

        return response()->json(['status' => 'success']);
    }

    public function getCategories(): JsonResponse
    {
        $helper = new TradeMeApiHelper();
        $groups = $helper->loadCategories();
        return response()->json($groups);
    }

    public function getSubCategories(Request $request): JsonResponse
    {
        $path = $request->get('path');
        $helper = new TradeMeApiHelper();
        $groups = $helper->loadSubCategories($path);
        return response()->json($groups);
    }

    public function groupsList(): AnonymousResourceCollection
    {
        $groups = TradeMeGroup::with('user')->orderBy('group_name')->get();
        return TradeMeGroupResource::collection($groups);
    }

    public function groupCreate(Request $request): AnonymousResourceCollection
    {
        TradeMeGroup::create([
            'group_name' => $request->input('group_name'),
            'trade_me_path' => $request->input('group_path'),
            'number_path' => $request->input('number_path'),
            'note' => $request->input('note'),
            'created_by' => $request->user()->id,
        ]);

        $groups = TradeMeGroup::with('user')->orderBy('group_name')->get();
        return TradeMeGroupResource::collection($groups);
    }

    public function groupDelete(TradeMeGroup $group): AnonymousResourceCollection
    {
        $group->delete();
        $groups = TradeMeGroup::with('user')->orderBy('group_name')->get();
        return TradeMeGroupResource::collection($groups);
    }

    public function templatesOptions(): JsonResponse
    {
        $duration = arrayToJsonFormat(TradeMeTemplate::DEFAULT_DURATION);
        $shipping = arrayToJsonFormat(TradeMeTemplate::SHIPPING_METHODS);
        $payments = arrayToJsonFormat(TradeMeTemplate::PAYMENT_METHODS);
        $tags = TradeMeTemplate::REPLACE_TAGS;
        $html = TradeMeTemplate::SUPPORTED_CHARACTERS;

        return response()->json(
            [
                'duration' => $duration,
                'shipping' => $shipping,
                'payments' => $payments,
                'tags' => $tags,
                'html' => $html,
            ]
        );
    }

    public function templatesList(): TradeMeTemplateResource
    {
        $template = TradeMeTemplate::first();
        return new TradeMeTemplateResource($template);
    }

    public function templateUpdate(Request $request): TradeMeTemplateResource
    {
        TradeMeTemplate::first()?->delete();
        $template = TradeMeTemplate::create([
            'title' => $request->input('title'),
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'delivery_options' => implode(',', $request->input('delivery_options')),
            'default_duration' => $request->input('default_duration'),
            'payments_options' => implode(',', $request->input('payments_options')),
            'update_prices' => (bool) $request->input('update_prices'),
            'relist' => (bool) $request->input('relist'),
        ]);
        return new TradeMeTemplateResource($template);
    }
}
