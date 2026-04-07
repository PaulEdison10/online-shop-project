import os
from pathlib import Path
from typing import Dict, List, Tuple

import numpy as np
import librosa


def list_audio_files(root_dir: str, extensions: Tuple[str, ...] = (".wav", ".mp3", ".flac")) -> List[str]:
	files: List[str] = []
	for dirpath, _, filenames in os.walk(root_dir):
		for fname in filenames:
			if fname.lower().endswith(extensions):
				files.append(str(Path(dirpath) / fname))
	return sorted(files)


def load_wav_mono_fixed(path: str, target_sr: int, target_num_samples: int) -> np.ndarray:
	audio, sr = librosa.load(path, sr=target_sr, mono=True)
	if audio.ndim > 1:
		audio = np.mean(audio, axis=0)
	# Pad or truncate to target length
	if len(audio) < target_num_samples:
		pad = target_num_samples - len(audio)
		audio = np.pad(audio, (0, pad), mode="constant")
	else:
		audio = audio[:target_num_samples]
	return audio.astype(np.float32)


def build_dataset_from_folders(data_dir: str, label_map: Dict[str, int], sample_rate: int, duration_seconds: float) -> Tuple[np.ndarray, np.ndarray, List[str]]:
	target_num_samples = int(sample_rate * duration_seconds)
	x_list: List[np.ndarray] = []
	y_list: List[int] = []
	class_order = sorted(label_map.keys(), key=lambda k: label_map[k])
	for label_name in class_order:
		class_dir = Path(data_dir) / label_name
		if not class_dir.exists():
			continue
		for wav_path in list_audio_files(str(class_dir)):
			signal = load_wav_mono_fixed(wav_path, sample_rate, target_num_samples)
			x_list.append(signal)
			y_list.append(label_map[label_name])
	if not x_list:
		raise RuntimeError(f"No audio files found under {data_dir}. Expected subfolders for labels: {list(label_map.keys())}")
	x = np.stack(x_list)  # [N, T]
	y = np.array(y_list, dtype=np.int64)
	# Add channel dimension for Conv1D: [N, T, 1]
	x = np.expand_dims(x, axis=-1).astype(np.float32)
	return x, y, class_order


def train_val_split(x: np.ndarray, y: np.ndarray, val_split: float = 0.2, seed: int = 42) -> Tuple[np.ndarray, np.ndarray, np.ndarray, np.ndarray]:
	n = x.shape[0]
	rng = np.random.default_rng(seed)
	indices = np.arange(n)
	rng.shuffle(indices)
	n_val = int(n * val_split)
	val_idx = indices[:n_val]
	train_idx = indices[n_val:]
	return x[train_idx], y[train_idx], x[val_idx], y[val_idx]