<?php

require_once __DIR__ .'/src/fallback_functions.php';
$f = fopen( 'php://stdin', 'r' );
$output = '';

function seperate(string $str) {
    $seperated = '';
    foreach(str_split($str) as $char)
        $seperated .="<span>$char</span>";
    return $seperated;
}

while( $line = fgets( $f ) ) {    
    $output .= $line;
}
ob_start();
eval(" ?>".$output."<?php ");
echo ob_get_clean();
fclose( $f );
?>