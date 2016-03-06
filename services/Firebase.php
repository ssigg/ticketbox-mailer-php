<?php

namespace Services;

class Firebase {
    private $_firebase;
    
    public function __construct($config) {
        $this->_firebase = new \Firebase\FirebaseLib($config['fburl'], $config['fbtoken']);
    }
    
    public function GetOrder($orderId) {
        $order = $this->_firebase->get('/orders/' + $orderId);
        return $order;
    }
    
    public function GetReservations($orderId) {
        $query = '/reservations?orderBy=orderId&startAt=' . $orderId . '&endAt=' . $orderId;
        $reservations = $this->_firebase->get($query);
        return $reservations;
    }
    
    public function GetEvent($eventId) {
        $event = $this->_firebase->get('/events/' . $eventId);
        return $events;
    }
}