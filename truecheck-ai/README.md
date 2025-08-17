# TrueCheck AI

AI-powered mobile app and platform to verify if media is real or AI-generated.

## Components
- Backend API: Node.js + Express (`backend/`)
- AI Inference Service: FastAPI (`ai/inference/server/`)
- Training pipelines: PyTorch (`ai/training/`)
- Mobile app: Flutter (`mobile/truecheck_app/`)
- Dashboard: Vite + React (`dashboard/web/`)

## Quick start (Dev)

### 1) Start AI inference service
```bash
cd ai/inference/server
python -m venv .venv && source .venv/bin/activate
pip install -r requirements.txt
uvicorn main:app --host 0.0.0.0 --port 9000
```

### 2) Start backend API
```bash
cd backend
cp .env.example .env
npm install
npm run dev
```

### 3) Start dashboard
```bash
cd dashboard/web
npm install
npm run dev
```
Dashboard will be served by Vite on 5173; backend also serves static dashboard under `/dashboard` if built.

### 4) Mobile app
Open `mobile/truecheck_app` in Flutter and run:
```bash
flutter pub get
flutter run -d emulator-5554
```
By default, the app targets backend at `http://10.0.2.2:8080` for Android emulator.

## API
- POST `/api/analyze?mediaType=image|video|audio` multipart form-data `file`
- GET `/api/report?id=...&label=...&confidence=...` PDF report
- GET `/api/stats` aggregated numbers
- GET `/api/recent` latest analyses

## Notes
- Heuristic inference is provided for demo. Replace with trained models and TFLite for on-device use.
- MongoDB is optional. Without it, the backend keeps data in memory.