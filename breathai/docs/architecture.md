# Architecture & Diagrams

## High-Level Components
- Mobile app (Flutter): audio capture, on-device TFLite inference, offline cache, background sync
- Backend (Node/Express + MongoDB): REST APIs, aggregation, dashboard hosting
- Dashboard (Leaflet): heatmap and analytics
- ML Pipeline (Python): training, evaluation, TFLite export
- Hardware: 3D-printed funnel improving SNR and hygiene

## Context Diagram (DFD Level 0)
```mermaid
flowchart LR
  CHW[Community Health Worker] -- breath audio --> Mobile
  Mobile -- risk & metadata --> Backend
  Backend -- aggregates --> Dashboard
  PublicHealth[Public Health Officer] -- view heatmap --> Dashboard
  Mobile -- offline use --> Mobile
```

## DFD Level 1: Mobile and Backend Flows
```mermaid
flowchart TB
  subgraph Mobile
    A[Record Breath Audio] --> B[Preprocess (normalize, pad)]
    B --> C[TFLite Inference]
    C --> D[Display Risk]
    D --> E[Store Locally (Hive)]
    E --> F[Sync Service]
  end

  subgraph Backend
    G[POST /api/screenings] --> H[MongoDB Save]
    I[GET /api/screenings/aggregate] --> J[Geo Aggregation]
    J --> K[Return Geohash Buckets]
  end

  F -- online --> G
  G --> I
```

## ERD (MongoDB schema)
```mermaid
erDiagram
  USER ||--o{ SCREENING : records
  USER {
    string _id
    string name
    string email
    string role
  }
  SCREENING {
    string _id
    string deviceId
    string userId
    date   timestamp
    float  latitude
    float  longitude
    string geohash
    number riskScore
    string riskLabel
    json   confidences
    string modelVersion
    string offlineId
    string notes
  }
```

## Use Cases (key)
- Record breath and pre-screen offline
- View on-device risk score
- Sync when online
- View public health heatmap

## Sequence: Offline first then sync
```mermaid
sequenceDiagram
  participant U as User
  participant M as Mobile App
  participant B as Backend
  participant DB as MongoDB

  U->>M: Start recording
  M->>M: Preprocess -> TFLite inference
  M->>U: Show risk result
  M->>M: Save result locally (offline)
  M->>B: Background sync when online
  B->>DB: Upsert screening
  B-->>M: 200 OK
```

## Deployment View
```mermaid
flowchart LR
  subgraph Device
    MobileApp[Flutter App + TFLite]
  end
  subgraph Cloud/Server
    Express[Node/Express]
    DB[(MongoDB)]
    Dashboard[Leaflet + React]
  end
  MobileApp <--> Express
  Express <--> DB
  Express --> Dashboard
```