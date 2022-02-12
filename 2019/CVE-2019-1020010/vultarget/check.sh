#!/bin/bash
sleep 300

echo "poc........................................."
config_result2=`docker logs poc`
echo "$config_result2"
if [ -z "$config_result2" ]; then
    echo "poc not success"
fi
