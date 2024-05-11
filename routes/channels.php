<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use AbdullahFaqeir\LaravelAgoraApi\Services\DisplayNameService;

Broadcast::channel(config('laravel-agora-api.channel_name'), static function ($user) {
    if (Auth::check()) {
        return [
            'id'   => Auth::id(),
            'name' => DisplayNameService::getDisplayName(Auth::user()),
        ];
    }

    return false;
});
