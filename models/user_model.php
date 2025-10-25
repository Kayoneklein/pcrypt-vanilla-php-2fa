<?php

class User extends DbConnection
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $public_key;
    public $server_url;
    public $device_id;
    public $server_id;
    public $created_at;
    public $updated_at;
    public $is_mobile_device;
    public $device_token;
    public $timestamp;

    ///PRIVATE FUNCTIONS START
    private function create_user_object_validated(): bool
    {
        if ($this->email && $this->password && $this->name) {
            return true;
        }
        return false;
    }

    private function login_user_object_validated(): bool
    {
        if ($this->email && $this->password) {
            return true;
        }
        return false;
    }
    ///PRIVATE FUNCTIONS END


    ///PUBLIC CLASS METHOD STARTS
    public function register()
    {
        if ($this->create_user_object_validated() == false) {
            http_response(400, 'Name, password and email are required');
        }

        $stmt = $this->conn->prepare("SELECT id FROM " . DBTables::$users . " WHERE email = ?");

        $stmt->execute([$this->email]);

        if ($stmt->fetch()) {
            http_response(400, 'Email already registered');
        }

        // Hash password
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $now = date('Y-m-d H:i:s');
        $this->created_at = $now;
        $this->updated_at = $now;

        $stmt = $this->conn->prepare(
            "INSERT INTO "
            . DBTables::$users . " 
        (email, password, name, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$this->email, $hashedPassword, $this->name, $this->created_at, $this->updated_at]);

        $this->login();
    }
    public function login()
    {

        if ($this->login_user_object_validated() == false) {
            http_response(400, 'Email and password are required');
        }
        $stmt = $this->conn->prepare("SELECT 
        id, email, password, name, public_key, device_id, server_id, server_url
        FROM " .
            DBTables::$users .
            " WHERE email = ?"
        );

        $stmt->execute([$this->email]);
        $user = $stmt->fetch();

        if ($user == null) {
            http_response(400, 'Invalid login credentials');
        }

        if ($this->email !== $user['email'] || !password_verify($this->password, $user['password'])) {
            http_response(400, 'Invalid login credentials');
        }
        $this->email = $user['email'];
        $this->name = $user['name'];
        $this->id = $user['id'];
        $this->public_key = $user['public_key'];
        $this->device_id = $user['device_id'];
        $this->server_id = $user['server_id'];
        $this->server_url = $user['server_url'];

        unset($user['password']);

        $token = generateJwtToken([
            'user_id' => $this->id,
            'email' => $this->email,
            'device_id' => $this->device_id,
            'server_id' => $this->server_id,
            'server_url' => $this->server_url,
        ]);

        http_response(200, [
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function get()
    {
    }

    public function update(array $data)
    {
        if (empty($data)) {
            return [
                'status' => false,
                'data' => 'No data provided for update.'
            ];
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        ///Add user id to the values as the update parameter;
        $values[] = $this->id;

        $stmt = "UPDATE " . DBTables::$users . " SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($stmt);

        $stmt->execute($values);
        $rows = $stmt->rowCount();


        if ($rows > 0) {
            return [
                'status' => true,
                'data' => 'User updated successfully.'
            ];
        }
        return [
            'status' => false,
            'data' => 'User update not successful'
        ];
    }
    ///PUBLIC CLASS METHOD END

    ///STATIC FUNCTIONS BELOW
    public static function fromAssoc($user_assoc): User
    {
        global $pdo;
        $user = new User($pdo);

        $user->email = $user_assoc['email'] ?? null;
        $user->id = $user_assoc['id'] ?? $user_assoc['user_id'] ?? null;
        $user->name = $user_assoc['name'] ?? null;
        $user->public_key = $user_assoc['public_key'] ?? null;
        $user->device_id = $user_assoc['device_id'] ?? null;
        $user->server_id = $user_assoc['server_id'] ?? null;
        $user->server_url = $user_assoc['server_url'] ?? null;
        $user->created_at = $user_assoc['created_at'] ?? null;
        $user->updated_at = $user_assoc['updated_at'] ?? null;
        $user->is_mobile_device = $user_assoc['is_mobile_device'] ?? null;
        $user->device_token = $user_assoc['device_token'] ?? null;
        $user->timestamp = $user_assoc['timestamp'] ?? null;

        return $user;
    }
}