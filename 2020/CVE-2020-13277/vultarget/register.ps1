# 注册 Runner 到 Gitlab

param([string]$TOKEN="")
if([String]::IsNullOrEmpty(${TOKEN})) {
    echo "Usage: .\register.ps1 TOKEN"
    echo "You can get the registration token from http://127.0.0.1/admin/runners"
    exit 1
}


$GITLAB_URL = "http://172.168.30.2"
$CONTAINER_NAME = "docker_runner"
$DOCKER_ID = (docker ps -aq --filter name=${CONTAINER_NAME})
if(![String]::IsNullOrEmpty(${DOCKER_ID})) {
    docker exec ${DOCKER_ID} /bin/sh -c "/usr/bin/gitlab-runner register --non-interactive --name poc-runner --executor shell --url ${GITLAB_URL} --registration-token ${TOKEN}"
}

echo "Done ."
exit 0