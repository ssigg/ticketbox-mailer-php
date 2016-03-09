<?php

require 'vendor/autoload.php';

require 'services/Firebase.php';
require 'services/Mailer.php';
require 'services/QRCodeCreator.php';

$config = json_decode(file_get_contents("config.json"), true);
$logger = new \Katzgrau\KLogger\Logger(__DIR__.'/logs');

// Create slim application
$app = new \Slim\Slim();

$app->post('/order', function() use ($app, $config, $logger) {
        $startTime = microtime(true);
        
        $data = json_decode($app->request->getBody(), true);
        $orderId = $data['orderId'];

        $firebase = new \Services\Firebase($config);
        $order = $firebase->GetOrder($orderId);
        if ($order == null) {
            $logger->error('Order not found.', $orderId);
            $app->response->setStatus(404);
        } else {
            $orderForLog = [
                'firstname' => $order['firstname'],
                'lastname' => $order['lastname'],
                'email' => $order['email'],
                'seats' => []
            ];
            
            $reservations = $firebase->GetReservations($orderId);
            $totalPrice = 0;
            foreach ($reservations as $key => $reservation) {
                $event = $firebase->GetEvent($reservation['eventId']);
                $seat = $firebase->GetSeat($reservation['seatId']);
                $eventBlock = $firebase->GetEventBlock($reservation['eventId'], $seat['blockId']);
                $category = $firebase->GetCategory($eventBlock['categoryId']);
                $price = $category['price'];
                $reservations[$key]['event'] = $event;
                $reservations[$key]['seat'] = $seat;
                $reservations[$key]['price'] = $price;
                $reservations[$key]['is_reduced'] = false;
                $totalPrice += $price;
                
                $orderForLog['seats'][] = [
                    'name' => $seat['name'],
                    'price' => $price,
                    'reduction' => false
                ];
            }
            
            $mailer = new \Services\Mailer($config);
            $mailer->SendOrderConfirmation($order, $reservations, $totalPrice);
            $mailer->SendOrderNotification($order, $reservations, $totalPrice);
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            $logger->info('Processed order successfully in '.$duration.'s.', $orderForLog);
            $app->response->setStatus(201);
        }
    });
    
$app->run();