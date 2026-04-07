# REST API Documentation

Base URL: `http://<server>:8080`

## Authentication
- Demo: Open endpoints (no auth)
- Production: Add JWT or API keys

## Models
- Screening
```
{
  "deviceId": "string",
  "userId": "string|null",
  "timestamp": "ISO-8601",
  "latitude": 12.9716,
  "longitude": 77.5946,
  "riskScore": 0.87,
  "riskLabel": "Abnormal",
  "confidences": {"Normal": 0.13, "Abnormal": 0.87},
  "modelVersion": "1.0.0",
  "offlineId": "uuid-from-mobile",
  "notes": "optional"
}
```

## Endpoints

### POST /api/screenings
Create or upsert a screening.
- Idempotency via `offlineId`.

Request
```
POST /api/screenings
Content-Type: application/json
{
  "deviceId": "and-12345",
  "timestamp": "2025-01-01T10:00:00Z",
  "latitude": 12.9716,
  "longitude": 77.5946,
  "riskScore": 0.72,
  "riskLabel": "Abnormal",
  "confidences": {"Normal": 0.28, "Abnormal": 0.72},
  "modelVersion": "1.0.0",
  "offlineId": "550e8400-e29b-41d4-a716-446655440000",
  "notes": "Dry cough"
}
```

Response
```
201 Created
{ "id": "65fd...", "status": "ok" }
```

### GET /api/screenings/aggregate?precision=6&sinceDays=30
Returns aggregated counts and average risk per geohash.

Response
```
200 OK
[
  {
    "geohash": "tdr5re",
    "count": 42,
    "avgRisk": 0.63,
    "lat": 12.9715,
    "lon": 77.5945
  },
  ...
]
```

### GET /api/screenings/stats
Simple stats for dashboard counters.

Response
```
200 OK
{
  "total": 133,
  "last24h": 18,
  "abnormalRate": 0.41
}
```

### Health
- `GET /healthz` -> `{ status: "ok" }`

## Errors
- `400` validation error
- `409` duplicate `offlineId`
- `500` server error

## Notes
- Precision: geohash precision 5–7 is reasonable for city-level heatmaps
- Rate limit writes in production; enable CORS for mobile origin