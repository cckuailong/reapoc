# CVE-2021-24545

현재는 plugin으로 배포가 중단되어 있는 플러그인에서 발견된 XSS 취약점입니다. 
WordPress Plugin HTML Author Bio description XSS

해당 취약점은 /wp-admin/profile.php에서 description 매개 변수에 대한 부적절한 유효성 검사로 인해 발생합니다.
원격의 공격자는 악의적으로 조작된 HTTP 요청을 전송하여 공격할 수 있다.

# 설치 및 실행 순서

#### 1. WordPress 설치
설치를 진행할 때, docker-compose.yml 파일에서 포트포워딩을 진행해주시기 바랍니다. 
<pre> $ docker-compose up  </pre>

#### 2. WordPress initial & Plugin installation
http://[web-server ip]:port/로 이동합니다.
기본적인 설치를 진행합니다.
WP-HTML-Author-Bio-master.zip 파일을 이용하여 플러그인을 설치합니다. 

#### 3. PoC

아래 경로로 들어갑니다.

http://[web-server ip]:port/wp-admin/profile.php

Biographical Info에 img 태그를 이용한 XSS payload를 삽입합니다. 

![image](https://user-images.githubusercontent.com/43310843/140014897-f2f7c6b9-3560-40ab-9120-2bd5311f8a43.png)

그리고 wordpress blog에 들어가면 아래 그림과 같이 XSS가 실행되는 것을 볼 수 있습니다.

![image](https://user-images.githubusercontent.com/43310843/140014892-4e7e6592-da5c-4fef-bfb5-0b70a0ee3164.png)

# 주의 사항
#### 위 취약점을 불법으로 악용할 시, 법적 책임을 지지 않습니다.
#### If you illegally exploit the above vulnerabilities, you will not be held liable.
#### docker 버전을 최신화 해야 합니다.

# 출처 
https://wpscan.com/vulnerability/64267134-9d8c-4e0c-b24f-d18692a5775e

