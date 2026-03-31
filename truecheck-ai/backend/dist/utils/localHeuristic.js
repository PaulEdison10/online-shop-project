"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.analyzeLocally = analyzeLocally;
function analyzeLocally(mediaType, buffer) {
    let hash = 0;
    for (let i = 0; i < buffer.length; i += Math.max(1, Math.floor(buffer.length / 1024))) {
        hash = (hash * 31 + buffer[i]) >>> 0;
    }
    const confidence = (hash % 100) / 100;
    let label = 'suspicious';
    if (confidence > 0.66)
        label = 'real';
    else if (confidence < 0.33)
        label = 'fake';
    return { label, confidence, details: { method: 'local_heuristic', mediaType, bytesSampled: Math.min(buffer.length, 1024) } };
}
