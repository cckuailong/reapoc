# CVE-2020-8277

For educational purposes only.

## Quick Run 
```
# clone this repository
$ git clone https://github.com/masahiro331/CVE-2020-8277

# run bind
$ docker build -t bind-local  ./bind
# Need TCP fallback
$ docker run --rm --name bind -it -p 53:53 -p 53:53/udp bind

# use "< v15.2.1" version
# If you use fixed version, build node.
$ git clone https://github.com/nodejs/node
$ git checkout df211208c0
$ ./configure
$ make -j8
$ make install

# Run PoC
$ node main.js
```

## Details

See Reference for the details.    
https://nodejs.org/en/blog/vulnerability/november-2020-security-releases/  
The advisory states that resolving a hostname that returns a large number of records will result in DoS.   


The vulnerability has read out of memory error.  
Affected line.   
https://github.com/nodejs/node/blob/1fd2c8142b611baadc973947b83c0863cb003d9d/src/cares_wrap.cc#L764  
