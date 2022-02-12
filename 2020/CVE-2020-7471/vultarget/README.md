# CVE-2020-7471
SQL injection via StringAgg delimeter input

# Setup:
Run `./setup.sh` for initial setup

Open the docker image to initiate the database:
`docker exec -it {container_id} /bin/bash`
And run the following commands:
```
python manage.py makemigrations vul_app
python manage.py migrate
```

Start the instances using: 
`docker-compose up`

Now open the following URL to load sample data:

http://localhost:8000/vul_app/setupdb

Then go to the vulnerable page at:
http://localhost:8000/vul_app/

Exploit the parameter at:
http://localhost:8000/vul_app/?delim=!@#
