<?php

//Include BaseX class
include_once( "Math/Basex.php" );

//mirror HEX character set an convert current code to new code
$newcode = convert_base("5c", "012345679abcdef", "fedcba9876543210");
echo $newcode . " (Result: a9)\n";
$newcode = Math_Basex::baseConvert("14", 10, 2);
echo $newcode . " (Result: 1110)\n";


function convert_base($code, $oldbase, $newbase)
{
    // take old base an input
 	$base = new Math_Basex($oldbase);
        
    //convert code to base10 decimal number
    $number = $base->toDecimal($code);
                
    //change to the new base
    $base->setBase($newbase);
                        
    //encode the decimal number and return the result to the function
    return $base->toBase($number);
}
                                                                
?>
