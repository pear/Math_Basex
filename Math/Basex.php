<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Dave Mertens <dmertens@zyprexia.com>                        |
// +----------------------------------------------------------------------+
//
// $Id$

require_once "Math/Basex/Exception.php";

/**
* base X coding class
*
* I noticed that value of an int is different on most systems. 
* On my system (linux 2.4.18 with glibc 2.2.5) i can use 8-byte integers 
* (also called int64 or int8)
* On my laptop (Windows 2000) i only could use numbers up to 4-byte (32 bit) 
* integers.
* So you might want to test this first!
*
* Note that you can without much effort also use the bcmath extentions to 
* increase the length of your numbers.
*
* @category Math
* @package  Math_Basex
* @author   Dave Mertens <dmertens@zyprexia.com>
* @version  0.4.0
* @access   public
* @link     http://pear.php.net/package/Math_Basex
*/
class Math_Basex
{
    /**
     * @var character base set
     * @access private;
     */
    protected $baseChars;

    /**
     * @var base length (for binair 2, dec 10, hex 16, yours ??)
     * @access private;
     */
    protected $length;

    protected $driver;
    
    /**
     * Constructor for class
     *
     * @param string $tokens Character base set (Each character is only allowed 
     *                                          once!)
     * @param string $driver One of 'bcmath', 'gmp' or 'none' (default)
     *
     * @return void
     */
    public function __construct($tokens = "", $driver = 'none')
    {
        //set initial length
        $this->length = 0;
        $this->setDriver($driver);
        //if we did get already a character set, set it..
        if (!empty($tokens)) {
            $this->setBase($tokens);
        }
    }

    /**
     * @param string $driver One of 'bcmath', 'gmp' or 'none' (default)
     */
    public function setDriver($driver) {
        $this->driver = $driver;
    }
            
    
    /**
     * Change the character base set. Behaves the same way the constructor does.
     *
     * @param string $tokens Character base set (Each character is only allowed 
     *                                          once!)
     *
     * @return void
     * @access public
     */
    public function setBase($tokens)
    {
        if (!$this->_checkBase($tokens)) {
            throw new Math_Basex_Exception("Each character is only allowed once");
        }
        $this->baseChars = $tokens;
        $this->length = strlen($tokens);
        return true;
    }
    
    /**
     * toBase translates a decimal (base 10) number into your base 'code'
     *
     * @param mixed $number (int64 or double without floats, both are 8-byte number 
     *         types). This allows you to use numbers up to 18446744073709551616.
     *
     * @return string encoded 'code' of yout decimal number
     */
    public function toBase($number)
    {
        if (!is_numeric($number)) {
            throw new Math_Basex_Exception("You must supply a decimal number");
        }
            
        if ($this->length == 0) {
            throw new Math_Basex_Exception("Character base isn't defined yet..");
        }
        if (is_float($number)) {
            $number = ltrim(sprintf('%22.0f', $number));
        }

        $code = "";
        do {
            $this->_splitnumber($number, $full, $mod);
            $code = $this->_getToken($mod) . $code;
            $number = $full;
        } while ($number > 0);
        
        return $code;
        
    }
    
    /**
     * toDecimal decodes the baseX 'code' back to a decimal number
     *
     * @param string $code code to decode
     * 
     * @return int64 decimal (base 10) number
     */
    public function todecimal($code)
    {
        $length = strlen($code);
        $total = 0;
        
        if (strspn($code, $this->baseChars) != $length) {
            throw new Math_Basex_Exception("Your Base X code contains invalid"
                                   ." characters");
        }

        for ($i=0; $i < $length; $i++) {
            $sum = $this->_getNumber($code[$length - $i - 1]) * 
                                     $this->_pow($this->length, $i);
            $total = $this->_add($total, $sum);
        }
        
        return $total;
    }
    
    /**
     * Returns the base scale. Note that this is onyl the count of the 
     * characters used for the encoding and decoding.
     * Please do not use base_convert with this class, because it might result 
     * in rare results
     *
     * @access public
     * @return integer
     */
    public function getBase()
    {
        return $this->length;
    }
    
    /**
     * Validates whether each character is unique
     *
     * @param string $tokens Character base set
     * 
     * @access private
     * @return boolean true if all characters are unique
     */
    protected function _checkBase($tokens)
    {
        $length = strlen($tokens);
        for ($i=0; $i < $length; $i++) {
            if (substr_count($tokens, $tokens[$i]) > 1)
                return false;    //character is specified more than one time!
        }
        
        //if we come here, all characters are unique
        return true;
    }
    
    /**
     * Helper function for encoding function. 
     *
     * @param int   $number   number to spilt for base conversion
     * @param int   &$full    non-float, unrounded number (will be passed as 
     *                        reference)
     * @param float &$modules floating number between 0 and 1 
     *                        (will be passed as reference)
     *
     * @access private
     * @return void
     */
    protected function _splitNumber($number, &$full, &$modules)
    {
        $full = $this->_div($number, $this->length);
        $modules = $this->_mod($number, $this->length);
    }

    /**
     * Helper function; Returns character at position x
     *
     * @param int $oneDigit number between 0 and basex->getBase()
     * 
     * @return character from base character set
     * @access private;
     */
    protected function _getToken($oneDigit)
    {
        return substr($this->baseChars, $oneDigit, 1);
    }
    
    /**
     * Helper function; Returns position of character X
     *
     * @param string $oneDigit Character in base character set
     * 
     * @return int number between 0 and basex->getBase()
     * @access private;
     */
    protected function _getNumber($oneDigit)
    {
        return strpos($this->baseChars, $oneDigit);
    }    

    /**
     * Add two numbers, utilize Math extensions
     *
     * @param mixed $a First operand
     * @param mixed $b Second operand
     * 
     * @return mixed
     * @access private
     */
    protected function _add($a, $b)
    {
        switch ($this->driver) {
        case 'bcmath':
            return bcadd($a, $b);
        case 'gmp':
            return gmp_strval(gmp_add($a, $b));
        case 'none':
            return $a + $b;
        }
    }

    /**
     * Multiply two numbers, utilize Math extensions
     *
     * @param mixed $a First operand
     * @param mixed $b Second operand
     * 
     * @return mixed
     * @access private
     */
    protected function _mul($a, $b)
    {
        switch ($this->driver) {
        case 'bcmath':
            return bcmul($a, $b);
        case 'gmp':
            return gmp_strval(gmp_mul($a, $b));
        case 'none':
            return $a * $b;
        }

    }

    /**
     * Return the modulo of two numbers, utilize Math extensions
     *
     * @param mixed $a First operand
     * @param mixed $b Second operand
     * 
     * @return mixed
     * @access private
     */
    protected function _mod($a, $b)
    {
        switch ($this->driver) {
        case 'bcmath':
            return bcmod($a, $b);
        case 'gmp':
            return gmp_strval(gmp_mod($a, $b));
        case 'none':
            return $a % $b;
        }
    }

    /**
     * Divide two integers, utilize Math extensions
     *
     * @param mixed $a First operand
     * @param mixed $b Second operand
     * 
     * @return mixed
     * @access private
     */
    protected function _div($a, $b)
    {
        switch ($this->driver) {
        case 'bcmath':
            return bcdiv($a, $b);
        case 'gmp':
            return gmp_strval(gmp_div($a, $b));
        case 'none':
            return floor($a / $b);
        }
    }

    /**
     * Raise one number to the power of the other, utilize Math extensions
     *
     * @param mixed $a First operand
     * @param mixed $b Second operand
     * 
     * @return mixed
     * @access private
     */
    protected function _pow($a, $b)
    {
        switch ($this->driver) {
        case 'bcmath':
            return bcpow($a, $b);
        case 'gmp':
            return gmp_strval(gmp_pow($a, $b));
        case 'none':
            return pow($a, $b);
        }
    }

    /**
     * Returns a common set of digits (0-9A-Za-z), length is given as parameter
     *
     * @param int $n Optional How many characters to return, defaults to 62.
     * 
     * @return string
     * @access public
     */
    public function stdBase($n = 62) 
    {
        return substr("0123456789"
                     ."ABCDEFGHIJKLMNOPQRSTUVWXYZ"
                     ."abcdefghijklmnopqrstuvwxyz", 0, $n);
    }

    /**
     * Converts a number from one base into another. May be called statically.
     * 
     * @param mixed  $number    The number to convert
     * @param int    $from_base The base to convert from
     * @param int    $to_base   The base to convert to
     * @param string $from_cs   Optional character set of the number that is
     *                                converted
     * @param string $to_cs     Optional character set of the target number
     * 
     * @return string
     * @access public
     */
    public function baseConvert($number, $from_base, $to_base, 
                          $from_cs = null, $to_cs = null)
    {
        $obj = $this;

        if (!isset($from_cs)) {
            $from_cs = $obj->stdBase();
        }
        if (!isset($to_cs)) {
            $to_cs = $obj->stdBase();
        }
        if (strlen($from_cs) < $from_base) {
            throw new Math_Basex_Exception('Character set isn\'t long enough for the'
                                   .'given base.');
        }
        if (strlen($to_cs) < $to_base) {
            throw new Math_Basex_Exception('Character set isn\'t long enough for the'
                                   .'given base.');
        }
        $from_cs = substr($from_cs, 0, $from_base);
        $to_cs   = substr($to_cs, 0, $to_base);
        if ($tmp = $obj->setBase($from_cs) !== true) {
            return $tmp;
        }
        $number = $obj->toDecimal($number);

        if ($tmp = $obj->setBase($to_cs) !== true) {
            return $tmp;
        }
        $number = $obj->toBase($number);
        return $number;
    }

}
