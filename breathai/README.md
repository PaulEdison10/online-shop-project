# BreathAI – AI-Powered, Offline-Ready Respiratory Disease Pre-Screening Tool

BreathAI is a mobile-first, offline-ready pre-screening solution that analyzes short breath recordings on-device using a TensorFlow Lite model to flag potential respiratory issues. It supports field data collection with offline sync, and a cloud dashboard for aggregated analytics and heatmaps.

## Features
- On-device inference (no internet required) with TFLite
- Record 10–20s breath sample via smartphone mic
- Risk score and label returned instantly on-device
- Offline store-and-forward sync to backend
- Public health dashboard with geospatial heatmaps
- Low-cost, hygienic 3D-printable breath funnel attachment

## Tech Stack
- AI/ML: Python, TensorFlow/TFLite, NumPy, Pandas, Librosa (for data prep), 1D CNN on raw audio
- Mobile: Flutter (Android/iOS), offline mode
- Backend: Node.js, Express, MongoDB (Dockerized)
- Dashboard: Leaflet + simple React (served by Express)
- Data: Coswara, ComParE (plus synthetic samples for demo)
- Hardware: OpenSCAD parametric funnel design

## Directory Structure
```
breathai/
  README.md
  DISCLAIMER.md
  docs/
    architecture.md
    implementation_guide.md
    api.md
    report.md
  ml/
    requirements.txt
    config.yaml
    label_map.json
    preprocess.py
    model.py
    train.py
    export_tflite.py
    infer_tflite.py
    data/
      README.md
      generate_synthetic.py
  mobile/
    pubspec.yaml
    lib/
      main.dart
      models/screening.dart
      services/
        audio_recorder.dart
        inference.dart
        sync_service.dart
      widgets/
        recording_card.dart
        result_card.dart
    assets/model/README.md
  backend/
    package.json
    tsconfig.json
    Dockerfile
    docker-compose.yml
    .env.example
    src/
      app.ts
      server.ts
      routes/
        screenings.ts
      controllers/
        screeningsController.ts
      models/
        Screening.ts
        User.ts
      utils/
        geohash.ts
  dashboard/
    package.json
    public/index.html
    src/
      index.js
      App.js
      components/Heatmap.js
  hardware/
    funnel.scad
    README.md
  scripts/
    demo_plan.md
```

## Quick Start

### 1) ML model (training + export)
- Create a venv, install `ml/requirements.txt`
- Generate small synthetic dataset to validate pipeline
- Train and export quantized TFLite model

Commands:
```bash
cd ml
python3 -m venv .venv && source .venv/bin/activate
pip install -r requirements.txt
python data/generate_synthetic.py --out ./data/synth --num_per_class 40 --sr 16000 --duration 2.0
python train.py --data_dir ./data/synth --epochs 10 --model_dir ./artifacts
python export_tflite.py --model_dir ./artifacts --out ./artifacts/breathai.tflite
```

Copy `ml/artifacts/breathai.tflite` to `mobile/assets/model/breathai.tflite`.

### 2) Backend + MongoDB
```bash
cd backend
cp .env.example .env
# Optionally edit MONGO_URI in .env
docker compose up -d --build
# Server runs on http://localhost:8080 (proxy if on device)
```

### 3) Dashboard (served by backend)
- The Express backend serves the React dashboard under `/dashboard`.
- Open `http://localhost:8080/dashboard` after backend starts.

### 4) Mobile app (Flutter)
- Ensure Flutter SDK installed
```bash
cd mobile
flutter pub get
flutter run
```
The app records audio, runs on-device TFLite, shows result, caches offline, and syncs to backend when online.

## Data & Labels
- Default demo labels: `Normal` (0), `Abnormal` (1) using synthetic and public samples
- Extendable to multi-class (e.g., Asthma/COPD/Bronchitis/TB/Post-COVID) with curated datasets

## Safety & Ethics
- Pre-screening aid only; not a medical device
- No PII required for inference; audio stays on device
- For research and educational use; clinical deployment requires regulatory approval

See `DISCLAIMER.md` for full notice.