Docker RosarioSIS
=================

## Installation

Minimum requirements: [Docker](https://www.docker.com/) & Git working.

You can pull the image from [DockerHub](https://hub.docker.com/r/rosariosis/rosariosis) or:

1. docker on
```bash
$ git clone https://gitlab.com/francoisjacquet/docker-rosariosis.git
$ cd docker-rosariosis
$ docker-compose up -d
```

2. Visit the URL and Install the Database

<pre>
http://YOURIP:80/InstallDatabase.php
</pre>

3. Than, Go to the [http://YOURIP:80/InstallDatabase.php]

4. Default admin/password is "admin/admin"

5. Go to the Scheduling -> Student Schedule 
<img src="https://user-images.githubusercontent.com/43310843/153820116-b8cc67b9-1ac3-4aff-95ee-837548bd2d27.png" width="70%" height="20%">


6. Course Choose and click the search
<img src="https://user-images.githubusercontent.com/43310843/153820615-eddcbe92-31c9-4d7a-ad8e-0d0b98edfa68.png" width="70%" height="20%">

7. Input the XSS payload 
<img src="https://user-images.githubusercontent.com/43310843/153820700-4be9143d-1dfd-4699-a7b6-28743d9ee940.png" width="70%" height="20%">

8. You can See the alert 
<img src="https://user-images.githubusercontent.com/43310843/153820773-c0d7901b-96f7-4e8d-bdfe-0de54d4dba2c.png" width="70%" height="20%">



#### referernce
https://github.com/86x/CVE-2021-45416
https://github.com/francoisjacquet/docker-rosariosis
