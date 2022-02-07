json-patch-php
================

Produce and apply json-patch objects.

Implements IETF JSON-patch (RFC 6902) and JSON-pointer (RFC 6901):

http://tools.ietf.org/html/rfc6902
http://tools.ietf.org/html/rfc6901

Using with Composer
-------------------

To use this library as a Composer dependency in your project, include the
following sections in your project's `composer.json` file:

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mikemccabe/json-patch-php"
        }
    ],
    "require": {
        "mikemccabe/json-patch-php": "dev-master"
    }
```

Then, in your project's code, use the `JsonPatch` class definition from
the `mikemccabe\JsonPatch` namespace like so:

```php
use mikemccabe\JsonPatch\JsonPatch;
```

Entry points
------------

- JsonPatch::get($doc, $pointer) - get a value from a json document
- JsonPatch::patch($doc, $patches) - apply patches to $doc and return result
- JsonPatch::diff($src, $dst) - return patches to create $dst from $src

Arguments are PHP arrays, i.e. the output of
json_decode($json_string, 1)

(Note that you MUST pass 1 as the second argument to json_decode to
get an array.  This library does not work with stdClass objects.)

All structures are implemented directly as PHP arrays.  An array is
considered to be 'associative' (e.g. like a JSON 'object') if it
contains at least one non-numeric key.

Because of this, empty arrays ([]) and empty objects ({}) compare the
same, and (for instance) an 'add' of a string key to an empty array
will succeed in this implementation where it might fail in others.

$simplexml_mode is provided to help with working with arrays produced
from XML in the style of simplexml - e.g. repeated XML elements are
expressed as arrays.  When $simplexml_mode is enabled, leaves with
scalar values are implicitly treated as length-1 arrays, so this test
will succeed:

    { "comment": "basic simplexml array promotion",
      "doc": { "foo":1 },
      "patch": [ { "op":"add", "path":"/foo/1", "value":2 } ],
      "expected": { "foo":[1, 2] } },

Also, when $simplexml_mode is true, 1-length arrays are converted to
scalars on return from patch().

Tests
-----

Some tests are in a submodule
(https://github.com/json-patch/json-patch-tests).  Do 'git submodule
init' to pull these, then 'php runtests.php' to run them.


[![Build Status](https://secure.travis-ci.org/mikemccabe/json-patch-php.png)](http://travis-ci.org/mikemccabe/json-patch-php)
