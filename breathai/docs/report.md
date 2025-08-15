# BreathAI – AI-Powered, Offline-Ready Respiratory Disease Pre-Screening Tool

## Abstract
Briefly summarize the motivation, offline on-device approach, datasets used (Coswara/ComParE), model, and key outcomes.

## 1. Introduction
- Background: Respiratory disease burden (WHO 2023)
- Gaps: Costly spirometry, rural access, late diagnosis
- Objectives: Offline pre-screening via breath audio, public health heatmaps
- Contributions: Mobile app, TFLite model, dashboard, funnel prototype

## 2. Literature Review
- Audio-based respiratory screening (wheeze/crackle detection)
- Coswara, ComParE respiratory audio datasets
- On-device ML (TFLite), privacy-preserving AI
- Geospatial surveillance and heatmap aggregation

## 3. System Design
- Architecture overview and component roles
- DFD, ERD, sequence diagrams (see `docs/architecture.md`)
- Data schema and API (see `docs/api.md`)
- Hardware design (funnel.scad)

## 4. Methodology
- Data preprocessing (resampling, normalization, padding)
- Model architecture (1D CNN on raw waveform)
- Training protocol (hyperparameters, splits, metrics)
- TFLite conversion and on-device inference
- Offline-first data sync and geohash aggregation

## 5. Implementation
- ML pipeline (files and commands)
- Mobile app (Flutter, permissions, local cache)
- Backend (Express + MongoDB, endpoints)
- Dashboard (Leaflet)
- Hardware (3D-print parameters)

## 6. Testing & Evaluation
- Unit tests for preprocessing and inference parity
- Validation metrics (accuracy, ROC-AUC)
- Latency tests on mobile hardware
- Load tests for backend aggregation
- Usability testing notes

## 7. Results
- Model metrics (validation accuracy)
- Example screenshots (app screen, dashboard heatmap)
- Error analysis and limitations

## 8. Discussion
- Strengths (offline, privacy, cost)
- Limitations (dataset mismatch with target population, noise sensitivity)
- Ethical considerations and data privacy

## 9. Conclusion
- Summary of findings and impact potential

## 10. Future Work
- Multi-class disease identification (asthma, COPD, TB)
- On-device feature extraction and lightweight architectures
- Federated learning
- Regulatory pathway and clinical validation

## References
- List IEEE/APA references for datasets and related work

## Appendix
- Detailed configs, additional plots, and instructions