import requests
import re


url = "http://bludit/install.php?language=en"
user = "admin"
password = "dxy0411"


def do_config():
    s = requests.Session()
    data = {
        "password": password
    }

    r = s.post(url, data, allow_redirects=False)
    # 禁止重定向
    if r.status_code != 302:
        print("[!] user install incorrect.")
        exit()

    print("[+] User Install successful.")
    return s


if __name__ == "__main__":
   do_config()