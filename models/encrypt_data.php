<?php

class EncryptData
{
    public $public_key;
    public $server_url;
    public $device_id;
    public $user_id;
    public $device_token;
    public $server_id;
    public $timestamp;
    public $is_mobile_device;

    public function __construct(
        $user_id,
        $server_url,
        $device_id,
        $public_key,
        $server_id,
        $timestamp,
        $device_token,
        $is_mobile_device
    ) {
        $this->user_id = $user_id;
        $this->server_url = $server_url;
        $this->device_id = $device_id;
        $this->public_key = $public_key;
        $this->server_id = $server_id;
        $this->timestamp = $timestamp;
        $this->device_token = $device_token;
        $this->is_mobile_device = $is_mobile_device;
    }

    public function serialize(): string
    {
        return json_encode($this);
    }
    public static function deserialize(string $data): EncryptData
    {

        $decoded = json_decode($data);

        return new EncryptData(
            $decoded->user_id,
            $decoded->server_url,
            $decoded->device_id,
            $decoded->public_key,
            $decoded->server_id,
            $decoded->timestamp,
            $decoded->device_token,
            $decoded->is_mobile_device,
        );
    }

}