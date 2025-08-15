import argparse
import json
import shutil
from pathlib import Path

import numpy as np
import tensorflow as tf
import yaml

from preprocess import build_dataset_from_folders, train_val_split
from model import build_1d_cnn_model


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument("--data_dir", required=True)
	parser.add_argument("--model_dir", default="./artifacts")
	parser.add_argument("--config", default="./config.yaml")
	parser.add_argument("--epochs", type=int, default=None)
	args = parser.parse_args()

	model_dir = Path(args.model_dir)
	model_dir.mkdir(parents=True, exist_ok=True)

	with open(args.config, "r") as f:
		cfg = yaml.safe_load(f)

	sample_rate = int(cfg["sample_rate"])
	duration_seconds = float(cfg["duration_seconds"])
	batch_size = int(cfg["batch_size"])
	epochs = int(args.epochs or cfg["epochs"])
	learning_rate = float(cfg["learning_rate"])
	val_split = float(cfg["validation_split"])
	model_version = str(cfg.get("model_version", "1.0.0"))

	# Load label map
	label_map_path = Path("./label_map.json")
	with open(label_map_path, "r") as f:
		label_map = json.load(f)

	x, y, class_order = build_dataset_from_folders(args.data_dir, label_map, sample_rate, duration_seconds)
	x_train, y_train, x_val, y_val = train_val_split(x, y, val_split=val_split)

	input_length = x.shape[1]
	num_classes = len(class_order)

	model = build_1d_cnn_model(input_length=input_length, num_classes=num_classes, learning_rate=learning_rate)

	early_stop = tf.keras.callbacks.EarlyStopping(patience=5, restore_best_weights=True, monitor="val_accuracy")
	checkpoint_path = model_dir / "ckpt.keras"
	checkpoint = tf.keras.callbacks.ModelCheckpoint(str(checkpoint_path), monitor="val_accuracy", save_best_only=True)

	history = model.fit(
		x_train,
		y_train,
		validation_data=(x_val, y_val),
		batch_size=batch_size,
		epochs=epochs,
		callbacks=[early_stop, checkpoint],
		verbose=2,
	)

	# Save final model
	saved_path = model_dir / "saved_model.keras"
	model.save(saved_path)

	# Save history and metadata
	with open(model_dir / "history.json", "w") as f:
		json.dump(history.history, f)

	metadata = {
		"sample_rate": sample_rate,
		"duration_seconds": duration_seconds,
		"input_length": input_length,
		"num_classes": num_classes,
		"classes": class_order,
		"model_version": model_version,
	}
	with open(model_dir / "metadata.json", "w") as f:
		json.dump(metadata, f)

	# Copy label_map.json into artifacts for mobile use
	shutil.copy(label_map_path, model_dir / "label_map.json")

	# Evaluate on validation set
	val_metrics = model.evaluate(x_val, y_val, verbose=0)
	print({"val_loss": float(val_metrics[0]), "val_accuracy": float(val_metrics[1])})


if __name__ == "__main__":
	main()