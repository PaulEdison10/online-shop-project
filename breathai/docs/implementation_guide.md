# Implementation Guide (Step-by-Step)

## 1. Plan & Scope
- Define target devices (Android first), audio duration (10–20s), sample rate (16 kHz)
- Start with binary classification (Normal vs Abnormal). Extend later.

## 2. ML Pipeline
1) Create Python venv and install `ml/requirements.txt`.
2) Generate a small synthetic dataset for smoke tests: `python data/generate_synthetic.py`.
3) Train the 1D CNN on raw audio: `python train.py --data_dir ...`.
4) Evaluate and export TFLite quantized model: `python export_tflite.py`.
5) Validate TFLite inference parity: `python infer_tflite.py`.

Artifacts: `ml/artifacts/{saved_model.h5, breathai.tflite, label_map.json}`

## 3. Mobile App (Flutter)
1) `flutter pub get`
2) Add `assets/model/breathai.tflite`.
3) Grant microphone permissions.
4) Record audio to WAV at 16 kHz mono.
5) Normalize, resample if needed, pad/truncate to fixed length.
6) Run TFLite inference, compute softmax, show label + confidence.
7) Save result locally (Hive) with location (if permitted).
8) Background sync queue to backend when online.

## 4. Backend & Dashboard
1) Copy `.env.example` to `.env`; set `MONGO_URI`.
2) `docker compose up -d --build`.
3) Backend serves API at `/api/*` and dashboard at `/dashboard`.
4) Test: `curl http://localhost:8080/api/screenings/stats | jq`.

## 5. Hardware Funnel
1) Open `hardware/funnel.scad` in OpenSCAD.
2) Adjust parameters (inner_diameter, length, wall_thickness).
3) Export STL and 3D print in PLA/PETG; use disposable filter gauze.

## 6. Testing & Validation
- Unit: Python preprocessing and inference parity; Dart WAV parsing & tensor shape
- Integration: Mobile -> Backend sync; Backend aggregation; Dashboard rendering
- Field simulation: Use synthetic and a quiet room; avoid wind noise

## 7. Documentation & Report
- Fill `docs/report.md` with study details, results, and screenshots
- Keep `docs/api.md` in sync with server routes

## 8. Future Extensions
- Move to multi-class with curated labels
- Add calibration step for device mic gain
- Add privacy-preserving on-device feature caching and federated learning