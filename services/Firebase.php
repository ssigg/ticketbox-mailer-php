<?php

namespace Services;

class Firebase {
    private $_firebase;
    
    public function __construct($config) {
        $this->_firebase = new \Firebase\FirebaseLib($config['fburl'], $config['fbtoken']);
    }
    
    public function GetOrder($orderId) {
        $order = $this->ConvertResponse($this->_firebase->get('/orders/' . $orderId));
        return $order;
    }
    
    public function GetReservations($orderId) {
        $reservations = $this->ConvertResponse($this->_firebase->get('/reservations'));
        $reservationsForOrder = [];
        foreach ($reservations as $reservation) {
            if ($reservation['orderId'] == $orderId) {
                $reservationsForOrder[] = $reservation;
            }
        }
        return $reservationsForOrder;
    }
    
    public function GetEvent($eventId) {
        $event = $this->ConvertResponse($this->_firebase->get('/events/' . $eventId));
        return $event;
    }
    
    public function GetSeat($seatId) {
        $seat = $this->ConvertResponse($this->_firebase->get('/seats/' . $seatId));
        return $seat;
    }
    
    public function GetEventBlock($eventId, $blockId) {
        $eventBlocks = $this->ConvertResponse($this->_firebase->get('/events/' . $eventId . '/blocks'));
        $eventBlocksForEventAndBlock = [];
        foreach ($eventBlocks as $eventBlock) {
            if ($eventBlock['blockId'] == $blockId) {
                return $eventBlock;
            }
        }
        return null;
    }
    
    public function GetCategory($categoryId) {
        $category = $this->ConvertResponse($this->_firebase->get('/categories/' . $categoryId));
        return $category;
    }
    
    private function ConvertResponse($responseAsJson) {
        $response = json_decode($responseAsJson, true);
        return $response;
    }
}