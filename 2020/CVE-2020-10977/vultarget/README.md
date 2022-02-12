# GitLab CVE2020-10977

## Introduction

This script provides remote code execution against GitLab Community Edition (CE) and Enterprise Edition (EE). The CVE is an arbitrary file read which allows you to extract the Rails `secret_key_base` by downloading the GitLab `secrets.yaml` file. Which in turn, enables you to gain code execution by signing your own `experimentation_subject_id` cookie that GitLab uses internally for A/B testing. The payload embedded in the cookie contains a deserialization vulnerability that allows running code on the GitLab instance.

> The arbitrary file read exists in GitLab EE/CE 8.5 and later. This got fixed in 12.9.1, 12.8.8 and 12.7.8. However, the RCE only affects version 12.4.0 and above when the vulnerable experimentation_subject_id cookie got introduced.

_Tested on 12.8.1_

## Usage

The module was tested with python 3.9 and requires the following dependencies:

- requests
- beautifulsoup4

If you have `pipenv` installed you can quickly get started by running `pipenv install` and `pipenv shell` to get a shell in the pipenv virtual environment.

```sh
 $ ./cve_2020_10977.py --help

usage: cve_2020_10977.py [-h] --url URL -u USERNAME -p PASSWORD [--cmd CMD]

optional arguments:
  -h, --help            show this help message and exit
  --url URL             Target URL
  -u USERNAME, --username USERNAME
                        Gitlab username
  -p PASSWORD, --password PASSWORD
                        Gitlab password
  --cmd CMD             Command to execute
```

## Development

A `Makefile` is included to ease local development or testing out the exploit. It depends on `docker` and `docker-compose` to quickly spin up a local version of GitLab that is vulnerable to this CVE.

```sh
make up
```

Will spin up a local instance of GitLab and a debian instance to make it easier to test out a reverse shell. The GitLab instance will be available from your localhost on port `5580`. Having this extra image gives you an IP that is reachable from within the docker network. It's possible to make your localhost reachable from the docker container, but not worth the effort IMO, and I definitely don't want to advertise using `--privileged`

Connecting to the RHOST debian instance can easily be done with:

```sh
make shell
```

This allows you to prepare you reverse shell with `nc -lnvp 9000`.

When this is done, you are ready to test out the exploit. You can run:

```sh
make exploit
```

To run the exploit, this will not do the RCE, but instead will print out the GitLab rails secret.

```sh
make exploit-rce
```

Will chain this CVE with the RCE payload mentioned above to get a reverse shell.

Happy hacking!

## References

- https://hackerone.com/reports/827052
- https://nvd.nist.gov/vuln/detail/CVE-2020-10977
- https://www.exploit-db.com/exploits/48431
