import argparse
import json
from pathlib import Path

import numpy as np
import tensorflow as tf

from preprocess import load_wav_mono_fixed


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument("--model", required=True, help="Path to .tflite model")
	parser.add_argument("--audio", required=True, help="Path to WAV file")
	parser.add_argument("--label_map", default="./label_map.json")
	parser.add_argument("--sr", type=int, default=16000)
	parser.add_argument("--duration", type=float, default=2.0)
	args = parser.parse_args()

	with open(args.label_map, "r") as f:
		label_map = json.load(f)
	inv_label_map = {v: k for k, v in label_map.items()}

	length = int(args.sr * args.duration)
	signal = load_wav_mono_fixed(args.audio, args.sr, length)
	signal = np.expand_dims(signal, axis=(0, -1)).astype(np.float32)  # [1, T, 1]

	interpreter = tf.lite.Interpreter(model_path=args.model)
	interpreter.allocate_tensors()
	input_details = interpreter.get_input_details()
	output_details = interpreter.get_output_details()

	interpreter.set_tensor(input_details[0]["index"], signal)
	interpreter.invoke()
	probs = interpreter.get_tensor(output_details[0]["index"])[0]
	pred_idx = int(np.argmax(probs))
	pred_label = inv_label_map.get(pred_idx, str(pred_idx))
	pred_score = float(probs[pred_idx])

	print(json.dumps({
		"label": pred_label,
		"score": pred_score,
		"probs": {inv_label_map[i]: float(p) for i, p in enumerate(probs)}
	}))


if __name__ == "__main__":
	main()