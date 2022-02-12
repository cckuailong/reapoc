import requests
import re
import sys
import json
#url="http://192.168.56.100:8000/login.php"
url="http://web/login.php"
user = "dxy"
password = "dxy0411"

def login():
    s = requests.Session()    
    set_page = s.get(url)
    CSRFName =re.search("'CSRFName\' value=\'(.+?)\'",set_page.text).group(1)
    CSRFToken = re.search("'CSRFToken\' value=\'(.+?)\'", set_page.text).group(1)
    data = {
        "tl_login": user,
        "tl_password": password,
        "CSRFName": CSRFName,
        "CSRFToken" : CSRFToken,
        "destination": "",
        "reqURI": ""
    }
    r = s.post(url, data, allow_redirects=False)
    if r.status_code != 200:
        print("[!] Username or password incorrect.")
        exit()
    print("[+] Loggin successful.")
    cookie=s.cookies.get_dict()
    f = open("temp", 'w+')
    print("{phpsession}".format(phpsession=cookie['PHPSESSID']), file=f)
    print("{testlink_cookie}".format(testlink_cookie=cookie['TESTLINK1920TESTLINK_USER_AUTH_COOKIE']), file=f)
    return s
    
if __name__ == '__main__':
    login()
  
