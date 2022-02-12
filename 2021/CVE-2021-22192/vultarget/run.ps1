docker-compose up -d

echo "Gitlab is starting ..."
echo "You can access the site (http://127.0.0.1) after 5 minutes ."


# 更改 Gitlab Pages 的 nginx 代理服务为 127.0.0.1:8000
sleep 300
$DOCKER_ID = (docker ps -aq --filter name=docker_gitlab)
if(![String]::IsNullOrEmpty($DOCKER_ID)) {
    docker exec -u root ${DOCKER_ID} /bin/bash -c "cp /var/opt/gitlab/nginx/conf/gitlab-pages.conf.local /var/opt/gitlab/nginx/conf/gitlab-pages.conf"
    docker exec -u root ${DOCKER_ID} /bin/bash -c "gitlab-ctl restart nginx"
}

echo "Gitlab is started ."
exit 0
