<?php

namespace AbdullahFaqeir\LaravelAgoraApi\Services;

use Exception;

class DisplayNameService
{
    public static function getDisplayName($user): string
    {
        $pieces = [];
        foreach (config('laravel-agora-api.user_display_name.fields') as $field) {
            $pieces[] = $user->{$field} ?? null;
        }
        return implode(config('laravel-agora-api.user_display_name.separator'), $pieces);
    }
}
