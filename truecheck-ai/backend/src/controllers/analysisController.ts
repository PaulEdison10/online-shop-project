import { Request, Response } from 'express';
import { z } from 'zod';
import { saveAnalysisResult } from '../services/resultService';
import { analyzeLocally } from '../utils/localHeuristic';

const analyzeQuerySchema = z.object({
  mediaType: z.enum(['image', 'video', 'audio'])
});

export async function analyzeMediaController(req: Request, res: Response) {
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

    const result = analyzeLocally(mediaType, file.buffer);

    const saved = await saveAnalysisResult({
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
  } catch (err: any) {
    console.error(err);
    res.status(500).json({ error: 'Analysis failed', message: err.message });
  }
}