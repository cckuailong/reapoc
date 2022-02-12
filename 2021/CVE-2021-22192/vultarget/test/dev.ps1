
# git clone --depth 1 --branch REL_2_3_1 https://github.com/gettalong/kramdown

docker-compose up -d
Sleep 5

docker run -it --rm -v "$PWD/kramdown:/kramdown" ruby /bin/bash