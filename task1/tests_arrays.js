
let rb = require("./reverse_binary_using_arrays.js");
let am /*AssertionsModule */ = require("./assert.js");

let assert = am.assert;
let assertEquals = am.assertEquals;

/* tests */

assertEquals(11, rb.reverseBinary(13));

// any power of 2 should return 1
// power of 2 + 1 returns the same pow of 2 + 1
for ( var pow = 1 ; pow < 53; pow++ ) { 
    assertEquals(1, rb.reverseBinary(2 ** pow));
    assertEquals(2 ** pow + 1, rb.reverseBinary(2 ** pow + 1));
}


/* output test results */
console.log(assert.numAssertions + " assertions, " + 
        ( assert.numFailedAssertions > 0 ? assert.numFailedAssertions : "none"  ) + " failed");