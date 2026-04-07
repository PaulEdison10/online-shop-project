import mongoose, { Schema, Document } from 'mongoose';

export interface ScreeningDoc extends Document {
	deviceId: string;
	userId?: string;
	timestamp: Date;
	latitude?: number;
	longitude?: number;
	geohash?: string;
	riskScore: number;
	riskLabel: string;
	confidences: Record<string, number>;
	modelVersion: string;
	offlineId: string;
	notes?: string;
	createdAt: Date;
}

const ScreeningSchema = new Schema<ScreeningDoc>({
	deviceId: { type: String, required: true },
	userId: { type: String },
	timestamp: { type: Date, required: true },
	latitude: { type: Number },
	longitude: { type: Number },
	geohash: { type: String, index: true },
	riskScore: { type: Number, required: true },
	riskLabel: { type: String, required: true },
	confidences: { type: Schema.Types.Mixed, required: true },
	modelVersion: { type: String, required: true },
	offlineId: { type: String, required: true, unique: true, index: true },
	notes: { type: String },
}, { timestamps: { createdAt: true, updatedAt: false } });

export default mongoose.model<ScreeningDoc>('Screening', ScreeningSchema);