<?php

class TwoFAModel extends DbConnection
{
    public $id;
    public $user_id;
    public $public_key;
    public $server_url;
    public $device_id;
    public $server_id;
    public $created_at;
    public $updated_at;
    public $is_mobile_device;
    public $device_token;
    public $timestamp;

    public function __construct($db, $user_id)
    {
        $this->conn = $db;
        $this->user_id = $user_id;
    }

    public function activate(EncryptData $data)
    {
        $payload = [
            'public_key' => $data->public_key,
            'server_url' => $data->server_url,
            'device_id' => $data->device_id,
            'device_token' => $data->device_token,
            'is_mobile_device' => $data->is_mobile_device,
            'timestamp' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'server_id' => generateUUIDv4(),
            'user_id' => $this->user_id,
        ];

        $stmt = $this->conn->prepare("SELECT id FROM " . DBTables::$two_fa . " WHERE user_id = ?");
        $stmt->execute([$this->user_id]);
        $twoFa = $stmt->fetch();

        ///Users that already have deviceId on the database has already activated 2FA. So they do not get to do activate it again
        if ($twoFa) {
            http_response(400, '2FA is active on this account');
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO "
            . DBTables::$two_fa . " 
        (public_key, server_url, device_id, device_token, is_mobile_device, timestamp, 
        created_at, updated_at, server_id, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $payload['public_key'],
            $payload['server_url'],
            $payload['device_id'],
            $payload['device_token'],
            $payload['is_mobile_device'],
            $payload['timestamp'],
            $payload['created_at'],
            $payload['updated_at'],
            $payload['server_id'],
            $payload['user_id'],
        ]);
        $two_fa_id = $this->conn->lastInsertId();
        if ($two_fa_id) {
            $payload["id"] = $two_fa_id;
            return $payload;
        }
        return null;
    }

    public function change_device(EncryptData $data)
    {
        $payload = [
            'public_key' => $data->public_key,
            'server_url' => $data->server_url,
            'device_id' => $data->device_id,
            'device_token' => $data->device_token,
            'is_mobile_device' => $data->is_mobile_device,
            'timestamp' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'server_id' => generateUUIDv4(),
        ];

        $stmt = $this->conn->prepare("SELECT * FROM " . DBTables::$two_fa . " WHERE user_id = ?");
        $stmt->execute([$this->user_id]);
        $twoFa = $stmt->fetch();

        if (!$twoFa) {
            http_response(400, '2FA is not active on this account');
        }

        $fields = [];
        $values = [];

        foreach ($payload as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $this->user_id;

        $stmt = "UPDATE " . DBTables::$two_fa . " SET " . implode(', ', $fields) . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($stmt);

        $stmt->execute($values);
        $rows = $stmt->rowCount();
        if ($rows) {
            $payload['user_id'] = $this->user_id;

            return $payload;
        }
        return null;
    }
}