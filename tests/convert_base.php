<?php

//Include BaseX class
include_once( "Math\Basex.php" );

//mirror HEX character set an convert current code to new code
$newcode = convert_base("5c", "012345679abcdef", "fedcba9876543210");
echo $newcode . "\n";


function convert_base($code, $oldbase, $newbase)
{
    // take old base an input
 	$base = new basex($oldbase);
        
    //convert code to base10 decimal number
    $number = $base->decode($code);
                
    //change to the new base
    $base->init($newbase);
                        
    //encode the decimal number and return the result to the function
    return $base->encode($number);
}
                                                                
?>