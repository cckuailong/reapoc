import requests
import re

#url='http://192.168.56.100:8000/firstLogin.php?viewer=new'
url="http://web/firstLogin.php?viewer=new"
user = "dxy"
password = "dxy0411"

def do_config():
    s = requests.Session()
    set_page = s.get(url)
    #print(set_page.text)
    CSRFName =re.search("'CSRFName\' value=\'(.+?)\'",set_page.text).group(1)
    CSRFToken = re.search("'CSRFToken\' value=\'(.+?)\'", set_page.text).group(1)
    data = {
        "login": user,
        "password": password,
        "password2": password,
        "firstName": 'every',
        "lastName": 'thing',
        "email": 'dxyabc@123.com',
        "doEditUser": "Add+User+Data",
        "CSRFName": CSRFName,
        "CSRFToken": CSRFToken}  
    r = s.post(url, data=data)
    #print(r.text)
    if r.status_code != 200:
        print("[!] user install incorrect.")
        exit()
    print("[+] User Install successful.")
    return s
    
if __name__ == "__main__":
    do_config()


