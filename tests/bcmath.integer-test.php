<?php
define('MATH_BASEX_MATHEXTENSION', 'bcmath');
include_once ( "Math/Basex.php" );
// PHASE 1: Simple toBase and toDecimal calls
$base = new Math_Basex("ABCDEF");
echo "Using character set: 'ABCDEF'\n";
echo validateResult("toBase(123456)", $base->toBase(123456), "CDFBDCA");
echo validateResult("toDecimal(\"BADA\")", $base->toDecimal("BADA"), "234");
echo "\n\n";

//PHASE 2: Testing int2
$base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
echo "Changing to character base 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'\n";
echo validateResult("int2 (Max 65536) toBase(65432)", 
                    $base->toBase(65432), "DSUQ");
echo validateResult("int2 (Max 65536) tobase(67000)  OVERFLOW!", 
                    $base->toBase(67000), "DVCY");

//PHASE 3: Testing int4
echo validateResult("int4 (Max 42944967297) toBase(64872767)", 
                    $base->toBase(64872767), "FLYZQL");
echo validateResult("int4 (Max 42944967297) tobase(43987654321) OVERFLOW!", 
                    $base->toBase(43987654309), "FMKGDIOB");

//PHASE $: Testing int8 
echo validateResult("int8 (Max 18446744073709551616)".
                    " toBase(18446744073709551610)", 
                    $base->toBase(1844674409510065), "NBTNBSZVFVD");
echo validateResult("int8 (Max 18446744073709551616)".
                    " tobase(18446744073709551618) OVERFLOW!", 
                    $base->toBase("18446744098897893117"), "HLHXCZQBGYKMWB");


function validateResult($description, $result, $compareString)
{
    if ((string)$result == (string)$compareString) {
        $ret = "$description\n - PASSED\n";
    } else {
        $ret = "$description\n - FAILED - result was $result, "
                                        ."expecting $compareString\n";
    }
    return $ret;

}

?>
