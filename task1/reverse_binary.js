"use strict";

/**
 * 
 * @param {number} n 
 */
function reverseBinary(n) {
    if ( typeof(n) != "number" ) {
        throw new Error("reverseBinary: given parameter must be a number");
    }
}

exports.reverseBinary = reverseBinary;