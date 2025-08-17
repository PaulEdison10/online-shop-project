"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
exports.saveAnalysisResult = saveAnalysisResult;
exports.getRecentResults = getRecentResults;
exports.getStats = getStats;
const mongoose_1 = __importStar(require("mongoose"));
const analysisSchema = new mongoose_1.Schema({
    mediaType: { type: String, required: true },
    filename: { type: String, required: true },
    mimeType: { type: String, required: true },
    sizeBytes: { type: Number, required: true },
    resultLabel: { type: String, required: true },
    confidence: { type: Number, required: true },
    details: { type: mongoose_1.Schema.Types.Mixed, default: {} }
}, { timestamps: { createdAt: true, updatedAt: false } });
let AnalysisModel = null;
function getAnalysisModel() {
    if (mongoose_1.default.connection.readyState === 1) {
        if (!AnalysisModel) {
            AnalysisModel = (0, mongoose_1.model)('Analysis', analysisSchema);
        }
        return AnalysisModel;
    }
    return null;
}
const memoryStore = [];
async function saveAnalysisResult(input) {
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
    const record = {
        id: String(memoryStore.length + 1),
        createdAt: new Date().toISOString(),
        ...input
    };
    memoryStore.push(record);
    return record;
}
async function getRecentResults(limit = 50) {
    const Model = getAnalysisModel();
    if (Model) {
        const docs = await Model.find().sort({ createdAt: -1 }).limit(limit).lean();
        return docs.map((d) => ({
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
async function getStats() {
    const data = await getRecentResults(1000);
    const total = data.length;
    const byLabel = { real: 0, fake: 0, suspicious: 0 };
    for (const r of data)
        byLabel[r.resultLabel]++;
    return { total, byLabel };
}
