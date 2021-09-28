
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

console.log(BigInt.asUintN(64, BigInt(128)));

assertEquals(11, rb.reverseBinary(13));

/* output test results */
console.log(assert.numAssertions + " assertions, " + 
        ( assert.numFailedAssertions > 0 ? assert.numFailedAssertions : "none"  ) + " failed");