# TrueCheck AI Training

This directory contains training pipelines for image, video, and audio deepfake detection.

- Image: XceptionNet/ViT fine-tuning for synthetic detection with JPEG artifact features.
- Video: Frame-level CNN + temporal aggregation; deepfake datasets (DFDC, FaceForensics++).
- Audio: Whisper embeddings + classifier; spectrogram CNN baseline.

Placeholders provided in `image/`, `video/`, `audio/` with minimal scripts. Integrate dataset paths and run accordingly.