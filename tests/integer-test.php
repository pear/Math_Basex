<?php

/*

Run this test to validate whether your system support int8 (64-bit) integers.
If int4 is the maximum of your system, maybe bcmath is an option for you!

*/

include_once ( "Math/Basex.php" );

// PHASE 1: Simple toBase and toDecimal calls
$base = new Basex("ABCDEF");
echo "Using character set: 'ABCDEF'\n";
echo validateResult("toBase(123456)", $base->toBase(123456), "CDFBDCA");
echo validateResult("toDecimal(\"BADA\")", $base->toDecimal("BADA"), "234");
echo "\n\n";

//PHASE 2: Testing int2
$base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
echo "Changing to character base 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'\n";
echo validateResult("int2 (Max 65536) toBase(65432)", $base->toBase(65432), "DSUQ");
echo validateResult("int2 (Max 65536) tobase(67000)  OVERFLOW!", $base->toBase(67000), "DVCY");

//PHASE 3: Testing int4
echo validateResult("int4 (Max 42944967297) toBase(64872767)", $base->toBase(64872767), "FLYZQL");
echo validateResult("int4 (Max 42944967297) tobase(43987654321) OVERFLOW!", $base->toBase(43987654321), "FMKGDIOB");

//PHASE $: Testing int8 
echo validateResult("int8 (Max 18446744073709551616) toBase(18446744073709551610)", $base->toBase(1844674407370955), "NBTNBSZVFVD");
echo validateResult("int8 (Max 18446744073709551616) tobase(18446744073709551618) OVERFLOW!", $base->toBase(18446744073709551618), "HLHXCZQBGYKMWB");


function validateResult($description, $result, $compareString)
{
	if ((string)$result == (string)$compareString)
		$ret = "PASSED";
	else
		$ret = "$result FAILED!!";
		

	return sprintf("%'.-72.72s %s\n", $description, $ret);
}

?>