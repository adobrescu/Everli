
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


/* output test results */
console.log(assert.numAssertions + " assertions, " + 
        ( assert.numFailedAssertions > 0 ? assert.numFailedAssertions : "none"  ) + " failed");