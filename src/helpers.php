<?php

if (!function_exists('packString')) {
    function packString($value): string
    {
        return pack("v", strlen($value)).$value;
    }
}
