"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = require("express");
const pdfkit_1 = __importDefault(require("pdfkit"));
const router = (0, express_1.Router)();
router.get('/report', async (req, res) => {
    const { id, label = 'suspicious', confidence = '0.5' } = req.query;
    const doc = new pdfkit_1.default({ size: 'A4', margin: 50 });
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
exports.default = router;
