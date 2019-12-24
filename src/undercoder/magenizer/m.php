<?php
namespace undercoder;

/**ERRORES PHP VISIBLES**/
error_reporting(E_ALL);
ini_set('display_errors', '1');

class Magenizer implements \Iterator
{
    /* @String */
    private $text;

    /* @Array */
    private $tokens;

    /* @Bool */
    private $reverse;

    private $currentToken;

    private $multiline;
    private $singleline;
    private $caseInsensitive;
    private $extended;
    private $last;
    private $ungreedy;
    private $stripSpaces;

    private $position;


    private $keyTokens;


    public function __construct(
        $txt,
        $tokens = array(),
        $reverse = false,
        $multiline = false,
        $singleline = true,
        $caseInsensitive = false,
        $extended = false,
        $ungreedy = true,
        $stripSpaces = true
    ) {
        $this->text    = $txt;
        $this->tokens  = $tokens;
        $this->reverse = $reverse;

        $this->multiline       = $multiline;
        $this->singleline      = $singleline;
        $this->caseInsensitive = $caseInsensitive;
        $this->extended        = $extended;

        $this->ungreedy     = $ungreedy;
        $this->$stripSpaces = $stripSpaces;

        $keyTokens = array_keys($this->tokens);

        if ($this->reverse) {
            $this->position = sizeof($this->keyTokens) -1;
        } else {
            $this->position = 0;
        }
    }

    public function rewind()
    {
        $this->position = ($this->reverse ? sizeof($this->keyTokens) -1 : 0);
    }

    public function current()
    {
        return $this->getToken(
            $this->keyTokens[ $this->position ],
            $this->token[ $this->keyTokens[ $this->position ] ][ "from" ],
            $this->token[ $this->keyTokens[ $this->position ] ][ "to" ]
        );
    }

    public function key()
    {
        return $this->keyTokens[ $this->position ];
    }

    public function next()
    {
        $this->position = $this->position + ($this->reverse ? -1 : 1);
    }

    public function prev()
    {
        $this->position = $this->position + ($this->reverse ? 1 : -1);
    }

    public function valid()
    {
        return isset($this->keyTokens[$this->position]);
    }

    private function getToken($token, $from, $to)
    {
        $flags = "";
        if ($this->ungreedy) {
            $flags .= "U";
        }
        if ($this->multiline) {
            $flags .= "m";
        }
        if ($this->singleline) {
            $flags .= "s";
        }
        if ($this->caseInsensitive) {
            $flags .= "i";
        }
        if ($this->extended) {
            $flags .= "x";
        }
        $pattern = "/{$from}.+{$to}/{$flags}";

        //var_dump($pattern);

        $tokenData = array();
        preg_match($pattern, $this->text, $tokenData);
        //var_dump($tokenData);

        if (is_array($tokenData) && !empty($tokenData) && array_key_exists(0, $tokenData)) {
            $ret = preg_replace("/{$from}/{$flags}", "", preg_replace("/{$to}/{$flags}", "", $tokenData[0]));
            if ($this->stripSpaces) {
                return trim($ret);
            }
            return $ret;
        }

        return false;
    }
}
