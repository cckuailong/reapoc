import requests
import re
import sys
import json

#目的：web初步配置 + admin用户注册 + admin用户登录并新建dxy用户

#url="http://192.168.56.100"
url="http://anchor_cms"
requests.adapters.DEFAULT_RETRIES = 5
s = requests.Session()  
s.keep_alive = False
  
def register_admin():
    data0 = {
	    "language": "en_GB",
	    "timezone": "UTC"
    }
    r = s.post(url+'/install/index.php?route=/start', data0)
    #host= 'http://anchor_cms_mysql'
    #host=re.search('(.+?)/install/index.php?route=/database', r.url).group(1)
    data1 = {
        "driver": 'mysql',
        "host": '192.168.56.100',
        #"host": host,
        "port": '3306',
        "user" : 'username',
        "pass": 'password',
        "name": "anchor_cms",
        "prefix": "anchor",
        "collation": 'utf8mb4_unicode_ci'
    }
    r = s.post(url+'/install/index.php?route=/database', data1)
    if r.status_code != 200:
        print("[!] database set incorrect.")
        exit()
    print("[+] database set successful.")

    data2 = {
	    "site_name": "My+First+Anchor+Blog",
	    "site_description": "It’s+not+just+any+blog.+It’s+an+Anchor+blog.",
	    "site_path": "/",
	    "theme": "default"
    }
    r = s.post(url+'/install/index.php?route=/metadata', data2)
    if r.status_code != 200:
        print("[!] metadata set incorrect.")
        exit()
    print("[+] metadata set successful.")

    data3 = {
	    "username": "admin",
	    "email": "admin@123.com",
	    "password": "123456"   
    }
    r = s.post(url+'/install/index.php?route=/account', data3)
    if r.status_code != 200:
        print("[!] account_admin register incorrect.")
        exit()
    print("[+] account_admin register successful.")
    
def login_admin(): 
    set_page = s.get(url+'/index.php/admin/login')
    token = re.search('name="token" type="hidden" value=\"(.+?)\"', set_page.text).group(1)
    data4 = {
	    "token": token,
	    "user": "admin",
	    "pass": "123456"
    }
    r = s.post(url+'/index.php/admin/login', data4)
    if r.status_code != 200:
        print("[!] account_admin loggin incorrect.")
        exit()
    print("[+] account_admin loggin successful.")

def register_victim():
    set_page = s.get(url+'/admin/users/add')
    token = re.search('name="token" type="hidden" value=\"(.+?)\"', set_page.text).group(1)
    data5 = {
        "token": token,
        "real_name": 'dxy',
        "bio": '',
        "status": 'active',
        "username": 'dxy',
        "password": '123456',
        "email": 'dxy@123.com'
    }
    r = s.post(url+'/admin/users/add', data5)
    if r.status_code != 200:
        print("[!] account_dxy register incorrect.")
        exit()
    print("[+] account_dxy register successful.")

if __name__ == '__main__':
    register_admin()
    login_admin()
    register_victim()
