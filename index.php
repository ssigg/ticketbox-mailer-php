<?php

require 'vendor/autoload.php';

require 'services/Firebase.php';
require 'services/Mailer.php';
require 'services/QRCodeCreator.php';

$config = json_decode(file_get_contents("config.json"), true);

// Create slim application
$app = new \Slim\Slim();

$app->post('/orders/confirmation', function() use ($app, $config) {
        $data = json_decode($app->request->getBody(), true);
        $orderId = $data['orderId'];

        // TODO: Get order and seats and calculate total price
        $firebase = new \Services\Firebase($config);
        $order = $firebase->GetOrder($orderId);
        $reservations = $firebase->GetReservations($orderId);
        $totalPrice = 0;
        foreach ($reservations as $reservation) {
            $event = $firebase->GetEvent($reservation['eventId']);
            $seat = $firebase->GetSeat($reservation['seatId']);
            $eventBlock = $firebase->GetEventBlock($reservation['eventId'], $reservation['blockId']);
            $category = $firebase->GetCategory($eventBlock['categoryId']);
            $price = $category['price'];
            $reservation['price'] = $price;
            $totalPrice += $price;
        }
        
        $mailer = new \Services\Mailer($config);
        $mailer->SendOrderConfirmation($order, $reservations, $totalPrice);
        $mailer->SendOrderNotification($order, $reservations, $totalPrice);
        $app->response->setStatus(201);
    });
    
$app->run();