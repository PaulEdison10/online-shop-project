from fastapi import FastAPI, UploadFile, File, Form
from fastapi.responses import JSONResponse
import io
from PIL import Image
import numpy as np
import cv2
import base64
import time

app = FastAPI(title="TrueCheck AI Inference Service", version="0.1.0")


def analyze_image_bytes(data: bytes):
	# Simple heuristics: JPEG compression artifacts + blur estimation
	try:
		img = Image.open(io.BytesIO(data)).convert('RGB')
		arr = np.array(img)
		# Blur via Laplacian variance
		lap_var = cv2.Laplacian(arr, cv2.CV_64F).var()
		# Color statistics
		mean_color = arr.mean()
		# Heuristic: extremely high smoothness may suggest synthetic
		blur_score = float(min(lap_var / 1000.0, 1.0))
		# Combine features into pseudo-confidence
		score = 0.5 * blur_score + 0.5 * (1.0 - abs(mean_color - 127.5) / 127.5)
		label = 'real' if score > 0.6 else ('fake' if score < 0.35 else 'suspicious')
		return label, float(max(0.0, min(score, 1.0))), {
			'laplacian_variance': lap_var,
			'mean_color': float(mean_color)
		}
	except Exception as e:
		return 'suspicious', 0.5, {'error': str(e)}


def analyze_video_bytes(data: bytes):
	# Extract first frame and reuse image heuristic
	tmp = np.frombuffer(data, np.uint8)
	video = cv2.imdecode(tmp, cv2.IMREAD_COLOR)
	if video is not None:
		# If uploaded as an image pretending to be video
		label, conf, details = analyze_image_bytes(data)
		details['note'] = 'Video decoded as single frame image.'
		return label, conf, details
	# Fallback: attempt to read frames using VideoCapture via memory (not supported directly)
	# Save to temp file alternative skipped in this demo. Return suspicious.
	return 'suspicious', 0.5, {'note': 'Video frame extraction not performed in demo.'}


def analyze_audio_bytes(data: bytes):
	# Simple spectral flatness + zero-crossing rate
	try:
		import scipy.signal as s
		arr = np.frombuffer(data, dtype=np.int16)
		if arr.size == 0:
			return 'suspicious', 0.5, {'error': 'Empty audio'}
		arr = arr.astype(np.float32) / 32768.0
		zcr = float((np.abs(np.diff(np.sign(arr))) > 0).mean())
		f, t, Sxx = s.spectrogram(arr, fs=16000, nperseg=256, noverlap=128)
		flatness = float(np.exp(np.mean(np.log(Sxx + 1e-8))) / (np.mean(Sxx) + 1e-8))
		score = 1.0 - min(1.0, (flatness + zcr) / 2.0)
		label = 'real' if score > 0.6 else ('fake' if score < 0.35 else 'suspicious')
		return label, float(score), {'zcr': zcr, 'spectral_flatness': flatness}
	except Exception as e:
		return 'suspicious', 0.5, {'error': str(e)}


@app.get("/health")
async def health():
	return {"status": "ok", "service": "truecheck-ai-inference", "time": time.time()}


@app.post("/infer")
async def infer(media_type: str = Form(...), file: UploadFile = File(...)):
	data = await file.read()
	if media_type == 'image':
		label, confidence, details = analyze_image_bytes(data)
	elif media_type == 'video':
		label, confidence, details = analyze_video_bytes(data)
	elif media_type == 'audio':
		label, confidence, details = analyze_audio_bytes(data)
	else:
		return JSONResponse(status_code=400, content={"error": "Unsupported media_type"})
	return {"label": label, "confidence": confidence, "details": details}