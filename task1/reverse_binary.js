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
    //@tbd is next validation needed?!
    if ( n < 0) {
        //do not deal with negative numbers (sign bit etc)
        throw new Error("reverseBinary: given parameter must be a positive number");
    }
    
    // convert n to a 64bit unsigned integer
    // if n is not an integer then an excception is thrown
    var bigintN, sbPosition, bigintReversedN;
    
    bigintN = BigInt.asUintN(64, BigInt(n));
    bigintReversedN = BigInt.asUintN(64, BigInt(0));
    sbPosition = getSignificantBitOffset(bigintN);
    var ctrlBitmask = 2n ** sbPosition;
    var pos = 0n;
    //deal with last sbPosition bits in bigintN
    for ( ctrlBitmask = 2n ** sbPosition;
        ctrlBitmask > 0n;
        ctrlBitmask >>= 1n, pos++) {
        
        if ( (ctrlBitmask & bigintN) == 0n ) {
            continue;
        }

        bigintReversedN |= ( 1n <<  pos );
    }
    return Number(bigintReversedN);
}
/**
 * Given a 64bit bigint number, returns the offset 
 * of the number's first bit set to 1.
 * 
 * Eg:
 * 
 * 0000 ...... 00001101
 * 
 * returns:
 * 
 * 3  
 * ( bitmask: 0000 .......00001000)
 * 
 * @param {*} n 
 */
function getSignificantBitOffset(n) {
    var ctrBitMask;
    var bitPosition;

    bitPosition = 63n;

    for ( ctrBitMask = BigInt.asUintN(64, 2n ** 63n); 
        ctrBitMask >= 0; 
        ctrBitMask >>= 1n, bitPosition-- ) {
        if ( ( n & ctrBitMask ) == ctrBitMask ) {
            break;
        }
    }

    return bitPosition;
}

exports.reverseBinary = reverseBinary;
exports.getSignificantBitOffset = getSignificantBitOffset;