import { Request, Response } from 'express';
import axios from 'axios';
import { z } from 'zod';
import FormData from 'form-data';
import { saveAnalysisResult } from '../services/resultService';

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

    let result: { label: 'real' | 'fake' | 'suspicious'; confidence: number; details?: Record<string, unknown> } = {
      label: 'suspicious',
      confidence: 0.5,
      details: { fallback: true, reason: 'AI service unavailable' }
    };

    try {
      const aiUrl = `${process.env.AI_SERVICE_URL || 'http://localhost:9000'}/infer`;
      const form = new FormData();
      form.append('file', file.buffer, { filename: file.originalname, contentType: file.mimetype });
      form.append('media_type', mediaType);
      const aiResponse = await axios.post(aiUrl, form, { headers: form.getHeaders(), maxBodyLength: Infinity });
      const data = aiResponse.data as any;
      if (data && typeof data.label === 'string' && typeof data.confidence === 'number') {
        result = { label: data.label, confidence: data.confidence, details: data.details };
      }
    } catch (err: any) {
      result.details = { ...(result.details || {}), error: err?.message || 'unreachable' };
    }

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