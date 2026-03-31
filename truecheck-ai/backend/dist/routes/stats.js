"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = require("express");
const resultService_1 = require("../services/resultService");
const router = (0, express_1.Router)();
router.get('/recent', async (_req, res) => {
    const data = await (0, resultService_1.getRecentResults)(50);
    res.json({ data });
});
router.get('/stats', async (_req, res) => {
    const stats = await (0, resultService_1.getStats)();
    res.json(stats);
});
exports.default = router;
