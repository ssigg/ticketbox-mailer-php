<?php

namespace Services;

class Mailer {
    private $_config;
    private $_latte;
    private $_mailer;
    private $_qrCodeCreator;

    public function __construct($config) {
        $this->_config = $config;
        $this->_latte = new \Latte\Engine;
        $this->_mailer = new \Nette\Mail\SendmailMailer;
        $this->_qrCodeCreator = new QRCodeCreator;
    }

    public function SendOrderConfirmation($order, $reservations, $totalPrice) {
        $params = [
            'order' => $order,
            'reservations' => $reservations,
            'totalPrice' => $totalPrice
        ];

        $template = __DIR__ . '/mails/OrderConfirmation.txt';
        $body = $this->_latte->renderToString($template, $params);
 
        $message = new \Nette\Mail\Message;
        $message
            ->setFrom($this->_config['addresses']['sender'])
            ->setSubject($this->_config['subjects']['order']['confirmation'])
            ->addReplyTo($this->_config['addresses']['reply'])
            ->addTo($order['email'])
            ->setBody($body);
        $this->_mailer->send($message);
    }

    public function SendOrderNotification($order, $reservations, $totalPrice) {
        $params = [
            'order' => $order,
            'reservations' => $reservations,
            'totalPrice' => $totalPrice
        ];

        $body = $this->_latte->renderToString(__DIR__ . '/mails/OrderNotification.txt', $params);

        $message = new \Nette\Mail\Message;
        $message
            ->setFrom($this->_config['addresses']['sender'])
            ->setSubject($this->_config['subjects']['order']['notification'])
            ->addReplyTo($this->_config['addresses']['reply'])
            ->addTo($this->_config['addresses']['watcher'])
            ->setBody($body);
        $this->_mailer->send($message);
    }
}