# [CVE-2021-37678](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-37678) Exploit

Para explotar la vulnerabilidad, es necesario levantar un contenedor docker con un ambiente preparado para levantar Tensorflow con una [versión 2.2.3](https://hub.docker.com/r/tensorflow/tensorflow/tags?page=1&ordering=last_updated&name=2.2.3) la cual todavía tiene la vulnerabilidad.
Consta de:
- Una arquitectura AMD de 64 bits,
- CPU o GPU que puede ejecutar instrucciones AVX
- Python de 64 bits en la versión 3.6.9
- Pip3 en la versión 20.2.4
- Tensorflow en la versión 2.2.3
- PyYAML en la versión 3.12


Verificar si la máquina cuenta con docker ejecutando:
```shell
docker -v
```

Una vez que nuestro sistema operativo tenga docker instalado, correr los siguientes comando en la carpeta raiz de este repositorio para construir y levantar la aplicación:

```shell
docker-compose build
```

```shell
docker-compose up
```

O bien, para levantar sin docker compose:

```shell
docker build -t docker-exploit-tensorflow-vulnerability:1.0 . # para construir la imagen
```
```shell
docker run --add-host=host.docker.internal:host-gateway --name exploit-tensorflow-vulnerability docker-exploit-tensorflow-vulnerability:1.0 # para crear y lanzar el proceso por primera vez
docker start -a exploit-tensorflow-vulnerability # para lanzar el proceso nuevamente
```

Para que el reverse shell funcione, hay que tener [netcat](https://en.wikipedia.org/wiki/Netcat) instalado en la máquina atacante, y levantar un puerto de escucha para una sesión ssh. Ej:
```shell
nc -lvp 10000
```
Luego, se explotará la vulnerabilidad mediante un modelo de IA malicioso, donde lanzaremos la conexión con el atacante.
Hay que configurarle la ip y puerto de escucha del atacante en el archivo [reverseShell.yaml](/src/reverseShell.yaml) 