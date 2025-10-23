<?php
use App\Controllers\AuthController;
use App\Controllers\LogController;

$app->post('/login', [AuthController::class, 'login']);
$app->post('/logout', [AuthController::class, 'logout']);
$app->post('/log-save', [LogController::class, 'save']);
$app->post('/log-connection-save', [LogController::class, 'saveConnection']);