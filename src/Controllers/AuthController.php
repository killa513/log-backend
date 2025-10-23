<?php
namespace App\Controllers;

use PDO;

class AuthController extends BaseController
{

    public static function login($request, $response)
    {
    $data = (array)$request->getParsedBody() ?? [];
    $identifier = $data['identifier'] ?? '';
    $password = $data['password'] ?? '';
    if (!$identifier || !$password) {
        $payload = json_encode(['success' => false, 'error' => 'Введите логин и пароль']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    $stmt = self::$pdo->prepare("SELECT * FROM users WHERE identifier = ?");
    $stmt->execute([$identifier]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$user || !isset($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        $payload = json_encode(['success' => false, 'error' => 'Неверный логин или пароль']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    $payload = json_encode(['success' => true, 'message' => 'Авторизация успешна']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    }


    public static function logout($request, $response, $args)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Вы вышли из системы']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
