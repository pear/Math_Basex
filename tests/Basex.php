<?php

//include BaseX class
include_once( "Math/Basex.php" );

//Create new instance
$base6 = new Math_Basex("ABCDEF");

//just calling some methods for testing Basex class.
echo "basex::toBase( 123456 )  --> " . $base6->toBase( 123456 ) . " (Result: CDFBDCA)\n";
echo "basex::toDecimal( \"BADA\" ) --> " . $base6->toDecimal( "BADA" ) . " (Result: 234)\n";


?>
