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
 */

class Magenizer implements \Iterator
{
    /* @String the text to be traversed */
    private $text;

    /* @Array the array containing the tokens used to search for data */
    private $tokens;

    /* @Bool indicates whether forwards or backwards. default false */
    private $reverse;

    /* @Bool flag for use the PCRE_MULTILINE (m) pattern modificator. default true  */
    private $multiline;

    /* @Bool flag for use the PCRE_DOTALL (s) pattern modificator. default true */
    private $singleline;

    /* @Bool flag for use the PCRE_CASELESS (i) pattern modificator. default false */
    private $caseInsensitive;

    /* @Bool flag for the use of the PCRE_EXTENDED (x) pattern modificator. default false */
    private $extended;

    /* @Bool flag for use the PCRE_UNGREEDY (U) pattern indicator. default true */
    private $ungreedy;

    /* @Bool indicates whether apply trim() to the resultant data. default true  */
    private $stripSpaces;

    /* @int internally stores the position of the current element*/
    private $position;

    /* @Array stores an array of tokens */
    private $keyTokens;


    public function __construct(
        $txt,
        $tokens,
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

        $this->ungreedy    = $ungreedy;
        $this->stripSpaces = $stripSpaces;

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
        $token = $this->keyTokens[ $this->position ];
        return $this->getToken(
            $this->keyTokens[ $this->position ],
            $this->tokens[ $token ][ "from" ],
            $this->tokens[ $token ][ "to" ]
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
