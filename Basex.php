<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
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

require_once "PEAR.php";

/**
* base X coding class
*
* I noticed that value of an int is different on most systems. 
* On my system (linux 2.4.18 with glibc 2.2.5) i can use 8-byte integers (also called int64 or int8)
* On my laptop (Windows 2000) i only could use numbers up to 4-byte (32 bit) integers.
* So you might want to test this first!
*
* Note that you can without much effort also use the bcmath extentions to increase the length of your numbers.
*
* @author Dave Mertens <dmertens@zyprexia.com>
* @version 1.0
* @access public
* @package math
*/
class Basex extends PEAR
{
	/**
	* @var character base set
	* @access private;
	*/
	var $_baseChars;

	/**
	* @var base length (for binair 2, dec 10, hex 16, yours ??)
	* @access private;
	*/
	var $_length;
	
	/**
	* Constructor for class
	*
	* @param tokens string Character base set (Each character is only allowed one!)
	* @return void
	*/
	function BaseX($tokens="")
	{
		//calling PEAR constructor..
		$this->PEAR();
		
		//set initial length
		$this->_length = 0;
		
		//if we did get already a character set, set it..
		if (isset($tokens))
			$this->setBase($tokens);
			
	}
	
	/**
	* Change the character base set. Behaves the same way the constructor does.
	*
	* @param tokens string Character base set (Each character is only allowed one!)
	* @return void
	* @access public
	*/
	function setBase($tokens)
	{
		if (isset($tokens))
		{
			if ($this->_checkBase($tokens))
				$this->_baseChars = $tokens;
			else
				return $this->raiseError("Each character is only allowed once");

			$this->_length = strlen($tokens);
		}	
	}
	
	/**
	* toBase translates a decimal (base 10) number into your base 'code'
	*
	* @param number (int64 or double without floats, both are 8-byte number types). This allows you to use numbers up to 18446744073709551616.
	* @return string encoded 'code' of yout decimal number
	*/
	function toBase($number)
	{
		$number = round($number, 0);	//this won't work on floating numbers...
		if (!is_numeric($number))
			return $this->raiseError("You must supply a decimal number");
			
		if ($this->_length == 0)
			return $this->raiseError("Character base isn't defined yet..");
			
		$code = "";
		do
		{
			if ($number > $this->length)
			{
				$this->_splitnumber($number, $full, $mod);
				$code = $this->_getToken($mod) . $code;
			}
			else
			{
				$code = $this->_getToken($number) . $code;
			}
			$number = $full;
							
		} while ($number > $this->length);
		
		return $code;
		
	}
	
	/**
	* toDecimal decodes the baseX 'code' back to a decimal number
	*
	* @param string code to decode
	* @return int64 decimal (base 10) number
	*/
	function todecimal($code)
	{
		$length = strlen($code);
		$total = 0;
		
		if (strspn($code, $this->_baseChars) != $length)
			return $this->raiseError("Your Base X code contains invalid characters");
		
		for ($i=0; $i < $length; $i++)
		{
			$sum = $this->_getNumber($code[$length - $i - 1]) * pow($this->_length, $i);
			$total += $sum;
		}
		
		return $total;
	}
	
	/**
	* Returns the base scale. Note that this is onyl the count of the characters used for the encoding and decoding.
	* Please do not use base_convert with this class, because it might result in rare results
	*
	* @access public
	* @return integer
	*/
	function getBase()
	{
		return $this->_length;
	}
	
	/**
	* Validates whether each character is unique
	*
	* @param string tokens Character base set
	* @access private
	* @return boolean true if all characters are unique
	*/
	function _checkBase($tokens)
	{
		$length = strlen($tokens);
		for ($i=0; $i < $length; $i++)
		{
			if (substr_count($tokens, $tokens[$i]) > 1)
				return false;	//character is specified more than one time!
		}
		
		//if we come here, all characters are unique
		return true;
	}
	
	/**
	* Helper function for encoding function. 
	*
	* @access private;
	* @param number integer number to spilt for base conversion
	* @param full integer non-float, unrounded number (will be passed as reference)
	* @param modules float floating number between 0 and 1 (will be passed as reference)
	*
	* @return void
	*/
	function _splitNumber($number, &$full, &$modules)
	{
		$full = floor($number / $this->_length);
		$modules = round($number % $this->_length, 4);
	}

	/**
	* Helper function; Returns character at position x
	*
	* @param oneDigit integer number between 0 and basex->getBase()
	* @return character from base character set
	* @access private;
	*/
	function _getToken($oneDigit)
	{
		return substr($this->_baseChars, $oneDigit, 1);
	}
	
	/**
	* Helper function; Returns position of character X
	*
	* @param oneDigit string Character in base character set
	* @return integer number between 0 and basex->getBase()
	* @access private;
	*/
	function _getNumber($oneDigit)
	{
		return strpos($this->_baseChars, $oneDigit);
	}	
}

?>