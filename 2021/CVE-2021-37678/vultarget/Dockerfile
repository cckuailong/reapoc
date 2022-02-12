FROM tensorflow/tensorflow:2.2.3
COPY . .
WORKDIR /src
#Instalacion de PyYAML
RUN pip install PyYAML==3.12

#Ejemplo feliz de carga de un modelo de ML de ejemplo y
#Explotación de vulnerabilidad cargando un modelo malicioso
CMD ["ExecutePythonScripts.sh"]
ENTRYPOINT ["bash"]
