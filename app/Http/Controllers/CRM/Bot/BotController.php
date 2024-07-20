<?php

namespace App\Http\Controllers\CRM\Bot;

use App\Events\Bot\SendBotMessageEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function sendMessage(Request $request): JsonResponse
    {
        event(new SendBotMessageEvent($request->input('message')));
        return response()->json([], 201);
    }
}
