import mongoose, { Schema, model, Model } from 'mongoose';

export type ResultLabel = 'real' | 'fake' | 'suspicious';

export interface AnalysisRecord {
  id: string;
  mediaType: 'image' | 'video' | 'audio';
  filename: string;
  mimeType: string;
  sizeBytes: number;
  resultLabel: ResultLabel;
  confidence: number; // 0..1
  details: Record<string, unknown>;
  createdAt: string;
}

const analysisSchema = new Schema(
  {
    mediaType: { type: String, required: true },
    filename: { type: String, required: true },
    mimeType: { type: String, required: true },
    sizeBytes: { type: Number, required: true },
    resultLabel: { type: String, required: true },
    confidence: { type: Number, required: true },
    details: { type: Schema.Types.Mixed, default: {} }
  },
  { timestamps: { createdAt: true, updatedAt: false } }
);

let AnalysisModel: Model<any> | null = null;

function getAnalysisModel(): Model<any> | null {
  if (mongoose.connection.readyState === 1) {
    if (!AnalysisModel) {
      AnalysisModel = model('Analysis', analysisSchema);
    }
    return AnalysisModel;
  }
  return null;
}

const memoryStore: AnalysisRecord[] = [];

export async function saveAnalysisResult(input: Omit<AnalysisRecord, 'id' | 'createdAt'>): Promise<AnalysisRecord> {
  const Model = getAnalysisModel();
  if (Model) {
    const doc = await Model.create(input);
    return {
      id: String(doc._id),
      mediaType: doc.mediaType,
      filename: doc.filename,
      mimeType: doc.mimeType,
      sizeBytes: doc.sizeBytes,
      resultLabel: doc.resultLabel,
      confidence: doc.confidence,
      details: doc.details || {},
      createdAt: doc.createdAt.toISOString()
    };
  }
  const record: AnalysisRecord = {
    id: String(memoryStore.length + 1),
    createdAt: new Date().toISOString(),
    ...input
  };
  memoryStore.push(record);
  return record;
}

export async function getRecentResults(limit = 50): Promise<AnalysisRecord[]> {
  const Model = getAnalysisModel();
  if (Model) {
    const docs = await Model.find().sort({ createdAt: -1 }).limit(limit).lean();
    return docs.map((d: any) => ({
      id: String(d._id),
      mediaType: d.mediaType,
      filename: d.filename,
      mimeType: d.mimeType,
      sizeBytes: d.sizeBytes,
      resultLabel: d.resultLabel,
      confidence: d.confidence,
      details: d.details || {},
      createdAt: d.createdAt.toISOString()
    }));
  }
  return [...memoryStore].sort((a, b) => (a.createdAt < b.createdAt ? 1 : -1)).slice(0, limit);
}

export async function getStats() {
  const data = await getRecentResults(1000);
  const total = data.length;
  const byLabel: Record<ResultLabel, number> = { real: 0, fake: 0, suspicious: 0 };
  for (const r of data) byLabel[r.resultLabel]++;
  return { total, byLabel };
}