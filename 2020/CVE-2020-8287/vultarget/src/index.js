const http = require('http')
const net = require('net')

const msg = [
  'POST / HTTP/1.1',
  'Host: 127.0.0.1',
  'Transfer-Encoding: chunked',
  'Transfer-Encoding: eee',
  '',
  '1',
  'A',
  '0',
  '',
  '',
].join('\r\n')

const server = http.createServer((req, res) => {
    let data = ''
    req.on('data', chunk => { data += chunk })
    req.on('end', () => {
        console.log({
          header: req.rawHeaders,
          body: data,
        })
        res.end()
    })
})

server.listen(0, () => {
  const client = net.connect(server.address().port, 'localhost')
  client.setEncoding('utf8')
  client.on('error', console.error)
  client.on('data', data => { console.log({ data }) })
  client.on('end', () => {
    client.destroy()
    server.close()
  })
  client.write(msg)
  client.resume()
})
