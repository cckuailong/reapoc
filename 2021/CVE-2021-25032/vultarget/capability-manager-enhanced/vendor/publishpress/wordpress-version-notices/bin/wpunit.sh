#!/bin/sh

bin/update-code-for-tests.sh

echo 'Running the wpunit tests'
echo '-------------------------------------------'
vendor/bin/codecept run wpunit