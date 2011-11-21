<?php
require_once "Math/Basex.php";
require_once "PHPUnit/Framework/TestCase.php";

class Math_Basex_IntegerTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->base = new Math_Basex("ABCDEF");
    }

    public function testToBase() {
        $this->base->setBase('ABCDEF');
        $this->assertSame((string)$this->base->toBase(123456), "CDFBDCA", "toBase(123456)");
    }

    public function testToDecimal() {
        $this->base->setBase('ABCDEF');
        $this->assertSame((string)$this->base->toDecimal("BADA"), "234", "toDecimal(BADA)");
    }

    public function testInt2() {
        $this->base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");

        $this->assertSame((string)$this->base->toBase(65432), "DSUQ", "int2 (Max 65536) toBase(65432)");
        $this->assertSame((string)$this->base->toBase(67000), "DVCY", "int2 (Max 65536) tobase(67000)  OVERFLOW!");
    }

    public function testInt4() {
        $this->base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $this->assertSame((string)$this->base->toBase(64872767), "FLYZQL", "int4 (Max 42944967297) toBase(64872767)");
        $this->assertSame((string)$this->base->toBase(43987654309), "FMKGDIOB", "int4 (Max 42944967297) tobase(43987654321) OVERFLOW!");
    }

    public function testInt8() {
        $this->base->setBase("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        //PHASE $: Testing int8 
        $this->assertSame((string)$this->base->toBase(1844674409510065), "NBTNBSZVFVD", "int8 (Max 18446744073709551616) toBase(18446744073709551610)");
        $this->assertSame((string)$this->base->toBase("18446744098897893117"), "HLHXCZQBGYKMWB", "int8 (Max 18446744073709551616) tobase(18446744073709551618) OVERFLOW!");
    }
}
