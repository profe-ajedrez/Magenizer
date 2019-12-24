<?php
namespace undercoder;

class TokenException extends Exception
{
    public function __construct($token, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)) {
            $this->message = "EL token [$token] ya fue agregado";
        }
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
