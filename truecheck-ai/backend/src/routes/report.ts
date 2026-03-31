import { Router, Request, Response } from 'express';
import PDFDocument from 'pdfkit';

const router = Router();

router.get('/report', async (req: Request, res: Response) => {
  const { id, label = 'suspicious', confidence = '0.5' } = req.query as Record<string, string>;
  const doc = new PDFDocument({ size: 'A4', margin: 50 });
  res.setHeader('Content-Type', 'application/pdf');
  doc.pipe(res);

  doc.fontSize(22).text('TrueCheck AI - Verification Report');
  doc.moveDown();
  doc.fontSize(12).text(`Report ID: ${id || 'N/A'}`);
  doc.text(`Date: ${new Date().toLocaleString()}`);
  doc.moveDown();
  doc.fontSize(16).text('Result', { underline: true });
  doc.moveDown(0.5);
  doc.fontSize(14).text(`Label: ${label.toUpperCase()}`);
  doc.text(`Confidence: ${(Number(confidence) * 100).toFixed(1)}%`);
  doc.moveDown();
  doc.fontSize(12).text('This report was generated automatically by TrueCheck AI to assist with media authenticity verification.');
  doc.end();
});

export default router;