import argparse
import os
import math
import random
from pathlib import Path

import numpy as np
import soundfile as sf


def generate_envelope(num_samples: int, sr: int) -> np.ndarray:
	# Slow inhale-exhale envelope using low-frequency sine and ramps
	t = np.linspace(0, num_samples / sr, num_samples, endpoint=False)
	lfo = 0.5 * (1 + np.sin(2 * np.pi * 0.25 * t))  # 0.25 Hz breathing rate
	attack = np.minimum(np.linspace(0, 1, num_samples // 10), 1)
	release = np.minimum(np.linspace(1, 0, num_samples // 5)[::-1], 1)
	env = lfo * 0.6 + 0.4
	env[: attack.shape[0]] *= attack
	env[-release.shape[0] :] *= release
	return env.astype(np.float32)


def generate_normal_breath(num_samples: int, sr: int) -> np.ndarray:
	# Soft broadband noise shaped by envelope
	noise = np.random.randn(num_samples).astype(np.float32) * 0.02
	env = generate_envelope(num_samples, sr)
	return (noise * env).astype(np.float32)


def generate_abnormal_breath(num_samples: int, sr: int) -> np.ndarray:
	# Add wheeze-like tone and intermittent crackles
	base = generate_normal_breath(num_samples, sr)
	# Wheeze: narrowband tone 400–1200 Hz
	freq = random.choice([400, 600, 800, 1000, 1200])
	t = np.linspace(0, num_samples / sr, num_samples, endpoint=False)
	wheeze = 0.02 * np.sin(2 * np.pi * freq * t).astype(np.float32)
	# Crackles: random short bursts
	crackles = np.zeros_like(base)
	for _ in range(random.randint(3, 8)):
		pos = random.randint(0, num_samples - int(0.02 * sr) - 1)
		dur = random.randint(int(0.005 * sr), int(0.02 * sr))
		crackles[pos : pos + dur] += (np.random.randn(dur).astype(np.float32) * 0.05)
	return (base + wheeze + crackles).astype(np.float32)


def save_wav(path: Path, audio: np.ndarray, sr: int) -> None:
	# Normalize peak to 0.9
	peak = np.max(np.abs(audio)) + 1e-7
	audio = 0.9 * audio / peak
	sf.write(str(path), audio, sr, subtype="PCM_16")


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument("--out", required=True, help="Output directory (will create class subfolders)")
	parser.add_argument("--num_per_class", type=int, default=40)
	parser.add_argument("--sr", type=int, default=16000)
	parser.add_argument("--duration", type=float, default=2.0)
	args = parser.parse_args()

	out_dir = Path(args.out)
	(out_dir / "Normal").mkdir(parents=True, exist_ok=True)
	(out_dir / "Abnormal").mkdir(parents=True, exist_ok=True)

	num_samples = int(args.sr * args.duration)
	for i in range(args.num_per_class):
		n = generate_normal_breath(num_samples, args.sr)
		a = generate_abnormal_breath(num_samples, args.sr)
		save_wav(out_dir / "Normal" / f"n_{i:03d}.wav", n, args.sr)
		save_wav(out_dir / "Abnormal" / f"a_{i:03d}.wav", a, args.sr)

	print(f"Wrote synthetic data to {out_dir}")


if __name__ == "__main__":
	main()