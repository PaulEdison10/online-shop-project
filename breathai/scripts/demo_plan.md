# Live Demo Simulation Plan

Duration: 8–10 minutes

1) Intro (1 min)
- Problem and target users
- Offline on-device approach

2) Model and pipeline (1 min)
- Show `ml/` structure, tiny dataset generation, and TFLite export

3) Backend up (1 min)
```bash
cd backend
cp .env.example .env
docker compose up -d --build
```
- Open `http://localhost:8080/healthz`

4) Dashboard (1 min)
```bash
cd dashboard
npm i
npm run build
```
- Visit `http://localhost:8080/dashboard`

5) Mobile app (3–4 min)
- `flutter run`
- Record 10s breath; show instant result
- Toggle airplane mode to show offline queue
- Turn online; auto-sync; refresh dashboard heatmap

6) Q&A (1–2 min)
- Safety/ethics, limitations, next steps