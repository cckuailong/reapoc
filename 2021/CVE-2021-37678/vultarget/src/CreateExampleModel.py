import tensorflow as tf

print('Se carga un set de datos que Google da como ejemplo para clasificación de imágenes\n\n')

mnist = tf.keras.datasets.mnist

(x_train, y_train), (x_test, y_test) = mnist.load_data()
x_train, x_test = x_train / 255.0, x_test / 255.0

print('\n\nSe crea un modelo de ML con una red neuronal para dicha clasificación de imágenes\n\n')

model = tf.keras.models.Sequential([
  tf.keras.layers.Flatten(input_shape=(28, 28)),
  tf.keras.layers.Dense(128, activation='relu'),
  tf.keras.layers.Dropout(0.2),
  tf.keras.layers.Dense(10)
])

predictions = model(x_train[:1]).numpy()

tf.nn.softmax(predictions).numpy()

loss_fn = tf.keras.losses.SparseCategoricalCrossentropy(from_logits=True)

loss_fn(y_train[:1], predictions).numpy()

model.compile(optimizer='adam',
              loss=loss_fn,
              metrics=['accuracy'])

print('\n\nEntrenamos el modelo con el set de datos que teníamos...\n\n')

model.fit(x_train, y_train, epochs=5)

model.evaluate(x_test,  y_test, verbose=2)

probability_model = tf.keras.Sequential([
  model,
  tf.keras.layers.Softmax()
])

probability_model(x_test[:5])

yaml_model = model.to_yaml()

print('\n\nEl modelo entrenado luce así en formato yaml...\n\n')

print(yaml_model)

json_model = model.to_json()

print('\n\nY luce así en formato json...\n\n')

print(json_model)

print('\n\nGuardamos el modelo en formato yaml...\n\n')

with open('exampleModel.yaml', 'w') as yaml_file:
    yaml_file.write(yaml_model)
