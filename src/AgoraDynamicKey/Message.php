<?php

namespace AbdullahFaqeir\LaravelAgoraApi\AgoraDynamicKey;

use Exception;

class Message
{
    public ?int $salt = null;

    public ?int $ts;

    public ?array $privileges = null;

    public function __construct()
    {
        try {
            $this->salt = random_int(1, 99999999);
        } catch (Exception) {
            $this->salt = 54760293754203;
        }

        $this->ts = now()->getTimestamp() + 24 * 3600;

        $this->privileges = [];
    }

    public function packContent(): bool|array
    {
        $buffer = unpack("C*", pack("V", $this->salt));
        $buffer = array_merge($buffer, unpack("C*", pack("V", $this->ts)));
        $buffer = array_merge($buffer, unpack("C*", pack("v", count($this->privileges))));
        foreach ($this->privileges as $key => $value) {
            $buffer = [
                ...$buffer,
                ...unpack("C*", pack("v", $key)),
                ...unpack("C*", pack("V", $value)),
            ];
        }

        return $buffer;
    }

    public function unpackContent(?string $msg): void
    {
        $pos = 0;
        $salt = unpack("V", substr($msg, $pos, 4))[1];
        $pos += 4;
        $ts = unpack("V", substr($msg, $pos, 4))[1];
        $pos += 4;
        $size = unpack("v", substr($msg, $pos, 2))[1];
        $pos += 2;

        $privileges = [];
        for ($i = 0; $i < $size; $i++) {
            $key = unpack("v", substr($msg, $pos, 2));
            $pos += 2;
            $value = unpack("V", substr($msg, $pos, 4));
            $pos += 4;
            $privileges[$key[1]] = $value[1];
        }
        $this->salt = $salt;
        $this->ts = $ts;
        $this->privileges = $privileges;
    }
}

