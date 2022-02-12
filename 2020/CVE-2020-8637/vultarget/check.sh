#!/bin/bash
config_result=`docker logs config`
echo "$config_result"
if [ -z "$config_result" ]; then
    echo "config not success"
fi

poc_result=`docker logs poc`
echo "$poc_result"
if [ -z "$poc_result" ]; then
    echo "poc not success"
fi