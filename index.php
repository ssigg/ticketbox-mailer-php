<?php

require 'vendor/autoload.php';

require 'services/Mailer.php';
require 'services/QRCodeCreator.php';

$config = json_decode(file_get_contents("config.json"), true);

// Create slim application
$app = new \Slim\Slim();

$app->post('/orders/confirmation', function() use ($app, $config) {
        $data = json_decode($app->request->getBody(), true);
        $orderId = $data['orderId'];

        // TODO: Get order and seats and calculate total price
        $mailer = new \Services\Mailer($config);
        $mailer->SendOrderConfirmation($order, $seats, $totalPrice);
        $mailer->SendOrderNotification($order, $seats, $totalPrice);
        $app->response->setStatus(201);
    });
    
$app->run();