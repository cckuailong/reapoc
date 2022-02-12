#!/bin/bash

poc_result=`docker logs poc`
echo "$poc_result"
if [ -z "$poc_result" ]; then
    exit 1
fi