"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.analyzeMediaController = analyzeMediaController;
const axios_1 = __importDefault(require("axios"));
const zod_1 = require("zod");
const form_data_1 = __importDefault(require("form-data"));
const resultService_1 = require("../services/resultService");
const analyzeQuerySchema = zod_1.z.object({
    mediaType: zod_1.z.enum(['image', 'video', 'audio'])
});
async function analyzeMediaController(req, res) {
    try {
        const parse = analyzeQuerySchema.safeParse(req.query);
        if (!parse.success) {
            return res.status(400).json({ error: 'Invalid query params', details: parse.error.flatten() });
        }
        const { mediaType } = parse.data;
        const file = req.file;
        if (!file) {
            return res.status(400).json({ error: 'Missing file' });
        }
        let result = {
            label: 'suspicious',
            confidence: 0.5,
            details: { fallback: true, reason: 'AI service unavailable' }
        };
        try {
            const aiUrl = `${process.env.AI_SERVICE_URL || 'http://localhost:9000'}/infer`;
            const form = new form_data_1.default();
            form.append('file', file.buffer, { filename: file.originalname, contentType: file.mimetype });
            form.append('media_type', mediaType);
            const aiResponse = await axios_1.default.post(aiUrl, form, { headers: form.getHeaders(), maxBodyLength: Infinity });
            const data = aiResponse.data;
            if (data && typeof data.label === 'string' && typeof data.confidence === 'number') {
                result = { label: data.label, confidence: data.confidence, details: data.details };
            }
        }
        catch (err) {
            result.details = { ...(result.details || {}), error: err?.message || 'unreachable' };
        }
        const saved = await (0, resultService_1.saveAnalysisResult)({
            mediaType,
            filename: file.originalname,
            mimeType: file.mimetype,
            sizeBytes: file.size,
            resultLabel: result.label,
            confidence: result.confidence,
            details: result.details || {}
        });
        res.json({
            id: saved.id,
            label: result.label,
            confidence: result.confidence,
            details: result.details || {},
            createdAt: saved.createdAt
        });
    }
    catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Analysis failed', message: err.message });
    }
}
