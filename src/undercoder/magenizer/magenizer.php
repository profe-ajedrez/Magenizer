<?php

namespace undercoder;


/**
 * Magenizer
 *
 * Implements Iterator interface over a String for extract data with an array of tokens.
 *
 * Given an array of tokens, iterates over a String, returning every time the corresponding data between the
 * corresponding tokens.
 *
 * Ex.:
 * $raw = <<<RAW
 * STRANGE Document With Weird LAYOUT
 * THIS document is ELECTRONIC
 * ANDRES REYES
 * N°3
 * WORK AT: HEROIC SERVICES FOR FREE,
 * ADDRESS  :  666, Where The Braves Dies Street, Santiago, Chile.
 * ...Some more of weird strings...
 * RAW;
 * $tokens = array(
 *   "name"        => array("from" => "ELECTRONIC",   "to" => "N\s*\°"),
 *   "doc-number"  => array("from" => "N\s*\°",       "to" => "WORK"),
 *   "job"         => array("from" => "K AT\:",       "to" => ",\nADDRESS"),
 *   "address"     => array("from" => "ADDRESS\s*\:", "to" => "\...Some")
 * );
 *
 * $mage = new Magenizer($raw, $tokens);
 * foreach ($m as $next) {
 *   echo "$next </br>";
 * }
 * //OUTPUT:
 * //------
 * //ANDRES REYES
 * //3
 * // HEROIC SERVICES FOR FREE
 * // 666, Where The Braves Dies Street, Santiago, Chile.
 *
 * A SHORT STORY
 * I wrote this class when at work I had to extract data from a raw string produced for the output of pdftotext.
 * This output was very irregular and its disposition changed from case to case, so I thought
 * the data as a target surrounded by a starting token and an ending token.
 * E.g. (the <> are only for the example): <name> Ermistuligio de las Mercedelines <number> 12 <weigth> 145 ...etc...
 * So, I could fetch the data pointing to the string between the tokens.
 * At that time, I was studying design patterns, so I implemented this solution using the iterator pattern.
 *
 */
class Magenizer implements \Iterator
{
    /* @String */
    private $text;

    /* @Array */
    private $tokens;

    /* @Bool */
    private $reverse;

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
        $multiline = true,
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

        $this->keyTokens = array_keys($this->tokens);


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
            $this->tokens[ $this->keyTokens[ $this->position ] ][ "from" ],
            $this->tokens[ $this->keyTokens[ $this->position ] ][ "to" ]
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

        $tokenData = array();
        preg_match($pattern, $this->text, $tokenData);

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
