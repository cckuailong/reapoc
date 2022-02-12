const express = require('express')
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


const app = express()
app.use((req, res) => {
  let data = ''
  req.on('data', chunk => { data += chunk })
  req.on('end', () => {
      console.log({
        header: req.headers,
        body: data,
      })
      res.end()
  })
})

const server = app.listen(3000, () => {
    const client = net.connect(3000, 'localhost')
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
