<?php

//include BaseX class
include_once( "Math/Basex.php" );

//Create new instance
$base6 = new basex("ABCDEF");

//just calling some methods for testing Basex class.
echo "basex::encode( 123456 )  --> " . $base6->encode( 123456 ) . " (Result: CDFBDCA)\n";
echo "basex::decode( \"BADA\" ) --> " . $base6->decode( "BADA" ) . " (Result: 234)\n";


?>