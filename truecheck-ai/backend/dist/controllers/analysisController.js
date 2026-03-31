"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.analyzeMediaController = analyzeMediaController;
const zod_1 = require("zod");
const resultService_1 = require("../services/resultService");
const localHeuristic_1 = require("../utils/localHeuristic");
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
        const result = (0, localHeuristic_1.analyzeLocally)(mediaType, file.buffer);
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
