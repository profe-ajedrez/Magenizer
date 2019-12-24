<?php
/**ERRORES PHP VISIBLES**/
error_reporting(E_ALL);
ini_set('display_errors', '1');
include "./src/undercoder/magenizer/magenizer.php";

use undercoder\magenizer;

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
  "job"         => array("from" => "AT\:",       "to" => "\,\nADDRESS"),
  "address"     => array("from" => "ADDRESS\s*\:", "to" => "\...Some")
);

$m = new Magenizer($raw, $tokens);


foreach ($m as $next) {
    echo "$next \n";
}
