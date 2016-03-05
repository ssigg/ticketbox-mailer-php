<?php

namespace Services;

class QRCodeCreator {
    const Width = 256;
    const Height = 256;

    private $_renderer;
    private $_writer;

    public function __construct() {
        $this->_renderer = new \BaconQrCode\Renderer\Image\Png();
        $this->_renderer->setHeight(self::Width);
        $this->_renderer->setWidth(self::Height);
        $this->_writer = new \BaconQrCode\Writer($this->_renderer);
    }

    public function getQRCodeString($value) {
        return $this->_writer->writeString($value);
    }
} 