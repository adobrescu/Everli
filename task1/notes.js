
const MAX_SAFE_NUMBER_64 = Number.MAX_SAFE_INTEGER;

const MAX_SAFE_UNSIGNED_NUMBER_64 = Number.MAX_SAFE_INTEGER;
const MAX_SAFE_UNSIGNED_NUMBER_32 = ( 2 ** 31 ) - 1;

var p = 32;
var n = (2 ** p) - 1;

console.log(MAX_SAFE_UNSIGNED_NUMBER_32, MAX_SAFE_UNSIGNED_NUMBER_32 >> 30, n, n >> ( p - 1), );