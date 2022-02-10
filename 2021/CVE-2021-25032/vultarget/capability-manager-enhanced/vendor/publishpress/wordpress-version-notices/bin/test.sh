#!/bin/sh

bin/update-code-for-tests.sh

echo 'Running the tests'
echo '-------------------------------------------'
vendor/bin/codecept run