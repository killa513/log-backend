<?php
namespace App\Controllers;

use PDO;

class LogController extends BaseController
{
    public static function save($request, $response)
    {
        $input = json_decode($request->getBody()->getContents(), true);

        if (!$input || !isset($input['type'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid log data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $type = $input['type'];
        $time = $input['time'] ?? date('Y-m-d H:i:s');
        $userAgent = $input['userAgent'] ?? null;
        $address = $input['address'] ?? null;
        $chainId = $input['chainId'] ?? null;
        $balance = $input['balance'] ?? null;
        $safes = isset($input['safes']) ? json_encode($input['safes']) : null;
        $message = $input['message'] ?? null;
        $data = json_encode($input, JSON_UNESCAPED_UNICODE);

        $stmt = self::$pdo->prepare("
            INSERT INTO wallet_logs (type, time, user_agent, address, chain_id, balance, safes, message, data)
            VALUES (:type, :time, :user_agent, :address, :chain_id, :balance, :safes, :message, :data)
        ");
        $stmt->execute([
            ':type' => $type,
            ':time' => date('Y-m-d H:i:s', strtotime($time)),
            ':user_agent' => $userAgent,
            ':address' => $address,
            ':chain_id' => $chainId,
            ':balance' => $balance,
            ':safes' => $safes,
            ':message' => $message,
            ':data' => $data,
        ]);

        $response->getBody()->write(json_encode(['status' => 'ok']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function saveConnection($request, $response)
{
    $input = json_decode($request->getBody()->getContents(), true);

    if (!$input || !isset($input['main_address']) || !isset($input['wallet_type'])) {
        $response->getBody()->write(json_encode(['error' => 'Invalid data']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $main = $input['main_address'];
    $multisig = $input['multisig_address'] ?? null;
    $type = $input['wallet_type'];

    $stmt = self::$pdo->prepare("
        INSERT INTO wallet_connections (main_address, multisig_address, wallet_type)
        VALUES (:main, :multi, :type)
    ");
    $stmt->execute([
        ':main' => $main,
        ':multi' => $multisig,
        ':type' => $type,
    ]);

    $response->getBody()->write(json_encode(['status' => 'ok']));
    return $response->withHeader('Content-Type', 'application/json');
}

public static function saveActivity($request, $response)
{
    $body = $request->getBody()->getContents();
    $input = json_decode($body, true);

    if (isset($input['logs']) && is_array($input['logs']) && isset($input['logs'][0])) {
        $input = $input['logs'][0];
    }

    if (!$input || !isset($input['event'])) {
        $response->getBody()->write(json_encode(['error' => 'Invalid data test']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $event = $input['event'] ?? null;
    $status = $input['status'] ?? null;
    $address = $input['address'] ?? null;

    $chainId = $input['chainId'] ?? ($input['chain_id'] ?? null);
    $safe = $input['safe'] ?? null;
    $balance = $input['balance'] ?? null;
    $approved = $input['approved'] ?? null;

    $rawMeta = $input['meta'] ?? [];
    if (is_string($rawMeta)) {
        $decoded = json_decode($rawMeta, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $rawMeta = $decoded;
        } else {
            $rawMeta = ['raw' => $rawMeta];
        }
    }
    $meta = json_encode($rawMeta, JSON_UNESCAPED_UNICODE);

    $time = date('Y-m-d H:i:s');

    $stmt = self::$pdo->prepare("
        INSERT INTO wallet_activity_logs (event, status, address, chain_id, safe, balance, approved, meta, created_at)
        VALUES (:event, :status, :address, :chainId, :safe, :balance, :approved, :meta, :time)
    ");

    $stmt->execute([
        ':event' => $event,
        ':status' => $status,
        ':address' => $address,
        ':chainId' => $chainId,
        ':safe' => $safe,
        ':balance' => $balance,
        ':approved' => $approved,
        ':meta' => $meta,
        ':time' => $time,
    ]);

    $response->getBody()->write(json_encode(['status' => 'ok']));
    return $response->withHeader('Content-Type', 'application/json');
}


}
