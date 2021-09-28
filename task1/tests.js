
let rb = require("./reverse_binary.js");

/**
 * Dummy assert function
 */
function assert(condition, failureMessage) {
    if (!condition ) {
        assert.numFailedAssertions++;
        console.log(failureMessage);
    }

    assert.numAssertions++;
}

/**
 * Strict type 
 */
function assertEquals (expectedValue, receivedValue) {
    assert(expectedValue === receivedValue, "Expected value: " + expectedValue + ", received: " + receivedValue);
}

assert.numAssertions = 0;
assert.numFailedAssertions = 0;

/* tests */

try {
    rb.reverseBinary("13");
    assert(false, "Parameter validation exception not thrown");
} catch (err) {
    assert(true, "Parameter validation exception thrown");
}

try {
    rb.reverseBinary(-13);
    assert(false, "Parameter validation (parameter >= 0 ) exception not thrown");
} catch (err) {
    assert(true, "Parameter validation (parameter >= 0 )  exception thrown");
}

try {
    rb.reverseBinary(13.05);
    assert(false, "Parameter validation (integer) exception not thrown");
} catch (err) {
    assert(true, "Parameter validation (integer)  exception thrown");
}

assertEquals(3n, rb.getSignificantBitOffset(13n)); // 0 ... 001101
assertEquals(4n, rb.getSignificantBitOffset(16n + 2n)); // 0 ... 010010
assertEquals(7n, rb.getSignificantBitOffset(255n));
assertEquals(8n, rb.getSignificantBitOffset(256n));

assertEquals(11, rb.reverseBinary(13));

// any power of 2 should return 1
// power of 2 + 1 returns the same pow of 2 + 1
for ( var pow = 1 ; pow < rb.MAX_SAFE_INTEGER_NUM_BITS; pow++ ) { 
    assertEquals(1, rb.reverseBinary(2 ** pow));
    assertEquals(2 ** pow + 1, rb.reverseBinary(2 ** pow + 1));
}


/* output test results */
console.log(assert.numAssertions + " assertions, " + 
        ( assert.numFailedAssertions > 0 ? assert.numFailedAssertions : "none"  ) + " failed");
