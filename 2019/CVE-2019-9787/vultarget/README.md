# CVE-2019-9787 CSRF PoC

## Overview
PoC of CSRF CVE-2019-9787
WordPress Version 5.1.1
[CVE-2019-9787](https://blog.ripstech.com/2019/wordpress-csrf-to-rce/)

Do not use this, EXCEPT for TEST purpose.

## Installation

```
docker-compose up -d
```

## Attack

1. Access http://localhost:8080/wp-admin/install.php and install WordPress. you only have to create WP admin account.

<p align="center">
  <img width="547" height="637" src="./screenshots/1.JPG">
</p>

2. Access http://localhost:8080/?p=1#comments as a visitor, and post comment like "Hacker Attack http://localhost/".

<p align="center">
  <img width="796" height="460" src="./screenshots/2.JPG">
</p>

<p align="center">
  <img width="711" height="642" src="./screenshots/3.JPG">
</p>

<p align="center">
  <img width="674" height="240" src="./screenshots/4.JPG">
</p>

3. Click the link posted at 2.

<p align="center">
  <img width="1206" height="131" src="./screenshots/5.JPG">
</p>

<p align="center">
  <img width="539" height="128" src="./screenshots/6.JPG">
</p>


4. You will see the comment "CSRF Attack made Successfully!" is posted by user you currently logged in.

<p align="center">
  <img width="1178" height="122" src="./screenshots/7.JPG">
</p>

<p align="center">
  <img width="665" height="459" src="./screenshots/8.JPG">
</p>
