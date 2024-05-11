<?php

namespace AbdullahFaqeir\LaravelAgoraApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use AbdullahFaqeir\LaravelAgoraApi\AgoraDynamicKey\RtcTokenBuilder;
use AbdullahFaqeir\LaravelAgoraApi\Events\AgoraCallAccepted;
use AbdullahFaqeir\LaravelAgoraApi\Events\DispatchAgoraCall;
use AbdullahFaqeir\LaravelAgoraApi\Events\RejectAgoraCall;
use AbdullahFaqeir\LaravelAgoraApi\Services\DisplayNameService;

class AgoraController extends Controller
{
    public function retrieveToken(Request $request): JsonResponse
    {
        $request->validate([
            'channel_name' => 'required|string',
        ]);

        /** @psalm-suppress NoInterfaceProperties */
        return response()->json([
            'token' => RtcTokenBuilder::buildTokenWithUid(config('laravel-agora-api.credentials.app_id'), config('laravel-agora-api.credentials.certificate'), $request->input('channel_name'), Auth::id(), RtcTokenBuilder::RoleAttendee, now()->getTimestamp() + 3600),
        ]);
    }

    public function placeCall(Request $request): void
    {
        $request->validate([
            'channel_name' => 'required|alpha_num',
            'recipient_id' => 'required|alpha_num',
        ]);

        broadcast(new DispatchAgoraCall($request->input('channel_name'), Auth::id(), DisplayNameService::getDisplayName(Auth::user()), $request->input('recipient_id')))->toOthers();
    }

    public function acceptCall(Request $request): void
    {
        $request->validate([
            'caller_id'    => 'required|alpha_num',
            'recipient_id' => 'required|alpha_num',
        ]);

        broadcast(new AgoraCallAccepted($request->input('caller_id'), $request->input('recipient_id')))->toOthers();
    }

    public function rejectCall(Request $request): void
    {
        $request->validate([
            'caller_id'    => 'required|alpha_num',
            'recipient_id' => 'required|alpha_num',
        ]);

        broadcast(new RejectAgoraCall($request->input('caller_id'), $request->input('recipient_id')))->toOthers();
    }
}
