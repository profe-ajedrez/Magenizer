# Magenizer

Implements Iterator interface over a String for extract data with an array of tokens. The name comes from Mage and tokenizer :)

Given an array of tokens, lets iterate over a String, returning every time the corresponding data between the corresponding tokens.

 Ex.:

 ```php
 $raw = <<<RAW
 STRANGE Document With Weird LAYOUT
 THIS document is ELECTRONIC
 ANDRES REYES
 N°3
 WORK AT: HEROIC SERVICES FOR FREE,
 ADDRESS  :  666, Where The Braves Dies Street, Santiago, Chile.
 ...Some more of weird strings...
 RAW;
 $tokens = array(
   "name"        => array("from" => "ELECTRONIC",   "to" => "N\s*\°"),
   "doc-number"  => array("from" => "N\s*\°",       "to" => "WORK"),
   "job"         => array("from" => "K AT\:",       "to" => ",\nADDRESS"),
   "address"     => array("from" => "ADDRESS\s*\:", "to" => "\...Some")
 );

 $mage = new Magenizer($raw, $tokens);
 foreach ($m as $next) {
   echo "$next </br>";
 }

 //OUTPUT:
 //------
 //ANDRES REYES
 //3
 // HEROIC SERVICES FOR FREE
 // 666, Where The Braves Dies Street, Santiago, Chile.
 ```

As you could see in the example, supports regexr, but without the delimiting _/_ because they are added internally.

@author Andrés Reyes

## Short Story

I wrote this class when at work I had to extract data from a raw string produced for the output of pdftotext.
This output was very irregular and its disposition changed from case to case, so I thought the data as a target surrounded by a starting token and an ending token.
E.g. (the <> are only for the example): `<name> Ermistuligius Of Bermelloauis <number> 12 <heigth> 145 ..etc...`
So, I could fetch the data pointing to the string between the tokens.
At that time, I was studying design patterns, so I implemented this solution using the iterator pattern.

## Disclaimer

* I'm a novice programmer, so feedback is always welcomed.
* Some coworkers ask me to submit this to Packagist.
* I'm a native Spanish speaker.
