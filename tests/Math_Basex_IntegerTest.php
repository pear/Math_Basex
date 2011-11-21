<?php
require_once "Math/Basex.php";
require_once "PHPUnit/Framework/TestCase.php";

class Math_Basex_IntegerTest extends PHPUnit_Framework_TestCase {

    public function data() {
        $data = array();

        $data[] = array(new Math_Basex('', "none"));
        $data[] = array(new Math_Basex('', "bcmath"));
        $data[] = array(new Math_Basex('', "gmp"));
        return $data;
    }

    /** @dataProvider data */
    public function testToBase(Math_Basex $base) {
        $base->setBase('ABCDEF');
        $this->assertSame("CDFBDCA", (string)$base->toBase(123456), "toBase(123456)");
    }

    /** @dataProvider data */
    public function testToDecimal(Math_Basex $base) {
        $base->setBase('ABCDEF');
        $this->assertSame("234", (string)$base->toDecimal("BADA"), "toDecimal(BADA)");
    }

    /** @dataProvider data */
    public function testInt2(Math_Basex $base) {
        $base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");

        $this->assertSame("DSUQ", (string)$base->toBase(65432), "int2 (Max 65536) toBase(65432)");
        $this->assertSame("DVCY", (string)$base->toBase(67000), "int2 (Max 65536) tobase(67000)  OVERFLOW!");
    }

    /** @dataProvider data */
    public function testInt4(Math_Basex $base) {
        $base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $this->assertSame("FLYZQL", (string)$base->toBase(64872767), "int4 (Max 42944967297) toBase(64872767)");
        $this->assertSame("FMKGDIOB", (string)$base->toBase(43987654309), "int4 (Max 42944967297) tobase(43987654321) OVERFLOW!");
    }

    /** @dataProvider data */
    public function testInt8(Math_Basex $base) {
        $base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        //PHASE $: Testing int8 
        $this->assertSame("NBTNBSZVFVD", (string)$base->toBase(1844674409510065), "int8 (Max 18446744073709551616) toBase(18446744073709551610)");
        $this->assertSame("HLHXCZQBGYKMWB", (string)$base->toBase("18446744098897893117"), "int8 (Max 18446744073709551616) tobase(18446744073709551618) OVERFLOW!");
    }

    /** @dataProvider data */
    public function testConvertBase(Math_Basex $base) {
        //mirror HEX character set an convert current code to new code
        $code = "5c";
        $oldbase = "012345679abcdef";
        $newbase = "fedcba9876543210";

        // take old base an input
     	$base->setBase($oldbase);
        
        //convert code to base10 decimal number
        $number = $base->toDecimal($code);

        //change to the new base
        $base->setBase($newbase);
                            
        //encode the decimal number and return the result to the function
        $newcode = $base->toBase($number);

        $this->assertSame('a9', $newcode);
        $newcode = $base->baseConvert("14", 10, 2);
        $this->assertSame('1110', $newcode);
    }

}
