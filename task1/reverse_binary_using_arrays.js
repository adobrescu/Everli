
/**
 * Shorter version (probably slower) using arrays.
 * Takes advantage of Number.toString(2) conversion to binary representation (as string) of a number
 * 
 */

/**
 * Given a number returns a new number obtained by reversing its binary representation using arrays.
 * 
 * @param {*} n 
 * @returns 
 */
function reverseBinary(n) {
    if ( typeof(n) != "number" || !Number.isInteger(n) ) {
        throw new Error("reverseBinary: given parameter must be an integer");
    }
    //@tbd is next validation needed?!
    if ( n < 0) {
        //do not deal with negative numbers (sign bit etc)
        throw new Error("reverseBinary: given parameter must be a positive number");
    }

    var nBinary, nInverseBinary;

    nBinary = n.toString(2); // n binary representation as string
    nInverseBinary = nBinary.split("").reverse().join("");

    return parseInt(nInverseBinary, 2);
}

exports.reverseBinary = reverseBinary;
