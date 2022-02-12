#! /usr/bin/env python
#
# Example: echo -n "BAhvOkBBY3RpdmVTdXBwb3J0OjpEZXByZWNhdGlvbjo6RGVwcmVjYXRlZEluc3RhbmNlVmFyaWFibGVQcm94eQk6DkBpbnN0YW5jZW86CEVSQgs6EEBzYWZlX2xldmVsMDoJQHNyY0kicSN7Y29kZX1AZW5jb2RpbmdJdToNRW5jb2RpbmcKVVRGLTgGOwpGOhNAZnJvemVuX3N0cmluZzA6DkBmaWxlbmFtZTA6DEBsaW5lbm9pADoMQG1ldGhvZDoLcmVzdWx0OglAdmFySSIMQHJlc3VsdAY7ClQ6EEBkZXByZWNhdG9ySXU6H0FjdGl2ZVN1cHBvcnQ6OkRlcHJlY2F0aW9uAAY7ClQ=--cb7aa07c89f2a281cee60b89269abcdffe2ff9d4" | decode-cookie.py

import argparse
import base64
import os
import sys
import stat
import urllib.parse


def is_input_redirected() -> bool:
    if os.isatty(sys.stdin.fileno()):
        return False
    else:
        mode = os.fstat(0).st_mode
        if stat.S_ISFIFO(mode):
            return True
        elif stat.S_ISREG(mode):
            return True
        else:
            return False


def main(args):
    if is_input_redirected():
        cookie_str = next(sys.stdin).strip()
    else:
        cookie_str = args.cookie

    cookie = urllib.parse.unquote(cookie_str.split("--")[0])
    enc = base64.urlsafe_b64decode(cookie).split(b"--")[0]
    print(enc)


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("-c", "--cookie", help="Rails cookie", type=str)
    args = parser.parse_args(sys.argv[1:])

    main(args)
