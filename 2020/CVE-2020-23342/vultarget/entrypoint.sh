#!/bin/bash
echo '[+] Wait For Anchor Installing…………(please wait until show "Anchor Install success!")'
composer clearcache
composer install
echo '[+] Anchor Install success!'

source /etc/apache2/envvars
apache2 -D FOREGROUND