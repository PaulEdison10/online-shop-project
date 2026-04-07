from typing import Tuple

import tensorflow as tf
from tensorflow.keras import layers, models


def build_1d_cnn_model(input_length: int, num_classes: int, learning_rate: float = 1e-3) -> tf.keras.Model:
	inputs = layers.Input(shape=(input_length, 1), name="waveform")

	# Block 1
	x = layers.Conv1D(16, 11, strides=2, padding="same", use_bias=False)(inputs)
	x = layers.BatchNormalization()(x)
	x = layers.ReLU()(x)
	x = layers.MaxPool1D(2)(x)

	# Block 2
	x = layers.Conv1D(32, 9, strides=1, padding="same", use_bias=False)(x)
	x = layers.BatchNormalization()(x)
	x = layers.ReLU()(x)
	x = layers.MaxPool1D(2)(x)

	# Block 3
	x = layers.Conv1D(64, 7, strides=1, padding="same", use_bias=False)(x)
	x = layers.BatchNormalization()(x)
	x = layers.ReLU()(x)
	x = layers.MaxPool1D(2)(x)

	# Block 4
	x = layers.Conv1D(128, 5, strides=1, padding="same", use_bias=False)(x)
	x = layers.BatchNormalization()(x)
	x = layers.ReLU()(x)

	x = layers.GlobalAveragePooling1D()(x)
	x = layers.Dropout(0.2)(x)
	x = layers.Dense(64, activation="relu")(x)
	outputs = layers.Dense(num_classes, activation="softmax", name="predictions")(x)

	model = models.Model(inputs=inputs, outputs=outputs, name="breathai_cnn_1d")
	model.compile(optimizer=tf.keras.optimizers.Adam(learning_rate=learning_rate),
				  loss="sparse_categorical_crossentropy",
				  metrics=["accuracy"])
	return model