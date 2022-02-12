# 生成 Gitlab License

$IMAGE_NAME = "gitlab_license"
$CONTAINER_NAME = "gen_gitlab_license"

echo "Building image ..."
docker build ./license -t ${IMAGE_NAME}
Sleep 2

echo "Generate gitlab license ..."
$IMAGE_ID = (docker image ls -aq --filter reference=${IMAGE_NAME})
docker run --name=${CONTAINER_NAME} ${IMAGE_ID} bash
Sleep 2

echo "Copy gitlab license to ./gitlab/keys"
$DOCKER_ID = (docker ps -aq --filter name=${CONTAINER_NAME})
if(![String]::IsNullOrEmpty(${DOCKER_ID})) {
    docker cp ${DOCKER_ID}:/opt/license_key ./gitlab/keys/license_key
    docker cp ${DOCKER_ID}:/opt/license_key.pub ./gitlab/keys/license_key.pub
    docker cp ${DOCKER_ID}:/opt/.gitlab-license ./gitlab/keys/.gitlab-license
    docker rm -f ${DOCKER_ID}
}

echo "Done ."
exit 0