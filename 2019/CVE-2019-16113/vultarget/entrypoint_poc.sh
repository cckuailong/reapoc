#!/bin/sh
nginx
python ./poc2.py
cat /var/log/nginx/access.log
