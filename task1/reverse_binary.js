"use strict";

/**
 * Reverse bits for a given number.
 *  
 * It reverses all or just  number's "significant" bits.
 * "Significant" bits start with the first bit that has a value of 1.
 * 
 *  
 * Eg. in 64 but representation of 13:
 * 
 * 00000  ....... 001101
 * 
 * The "significant" bits are the last 4:
 * 
 * 1101
 * 
 * 
 * Eg:
 * 
 * Given a number on 64 bit: 
 * 000000 ...... 00001101 (64 bit for "13")
 * 
 * - reverse "significant" bits:
 * 000000 ....... 00001011 (64 bit for "11") 
 * 
 * - reverse all bits:
 * 110100 ....... 00000000
 * 
 * Number of bits of a number is 64:
 * https://www.w3schools.com/js/js_numbers.asp
 * 
 * @param {number} n 
 */
function reverseBinary(n) {
    if ( typeof(n) != "number" ) {
        throw new Error("reverseBinary: given parameter must be a number");
    }
    if ( n < 0) {
        //do not deal with negative numbers (sign bit etc)
        throw new Error("reverseBinary: given parameter must be a positive number");
    }
    
}

exports.reverseBinary = reverseBinary;