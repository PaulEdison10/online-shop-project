import argparse
from pathlib import Path

import tensorflow as tf


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument("--model_dir", required=True)
	parser.add_argument("--out", required=True)
	args = parser.parse_args()

	model_path = Path(args.model_dir) / "saved_model.keras"
	if not model_path.exists():
		raise FileNotFoundError(f"Model not found at {model_path}")

	model = tf.keras.models.load_model(model_path)
	converter = tf.lite.TFLiteConverter.from_keras_model(model)
	converter.optimizations = [tf.lite.Optimize.DEFAULT]
	# Dynamic range quantization keeps float input/output; good for general devices
	tflite_model = converter.convert()

	out_path = Path(args.out)
	out_path.parent.mkdir(parents=True, exist_ok=True)
	with open(out_path, "wb") as f:
		f.write(tflite_model)
	print(f"Wrote TFLite model to {out_path}")


if __name__ == "__main__":
	main()