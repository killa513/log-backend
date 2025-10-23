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

}
