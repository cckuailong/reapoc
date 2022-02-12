mv cve20207471/urls.py .
mv cve20207471/settings.py .
sudo docker-compose run web django-admin startproject cve20207471 .
mv -f urls.py cve20207471/
mv -f settings.py cve20207471/ 
