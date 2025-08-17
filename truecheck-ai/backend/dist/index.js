"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
require("dotenv/config");
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors"));
const morgan_1 = __importDefault(require("morgan"));
const mongoose_1 = __importDefault(require("mongoose"));
const path_1 = __importDefault(require("path"));
const analysis_1 = __importDefault(require("./routes/analysis"));
const report_1 = __importDefault(require("./routes/report"));
const stats_1 = __importDefault(require("./routes/stats"));
const app = (0, express_1.default)();
app.use((0, cors_1.default)());
app.use(express_1.default.json({ limit: '20mb' }));
app.use((0, morgan_1.default)('dev'));
const mongoUri = process.env.MONGODB_URI;
if (mongoUri) {
    mongoose_1.default
        .connect(mongoUri)
        .then(() => console.log('MongoDB connected'))
        .catch((err) => console.error('MongoDB connection error:', err));
}
else {
    console.log('MONGODB_URI not set. Using in-memory store.');
}
app.get('/api/health', (_, res) => {
    res.json({ status: 'ok', service: 'truecheck-backend', time: new Date().toISOString() });
});
app.use('/api', analysis_1.default);
app.use('/api', report_1.default);
app.use('/api', stats_1.default);
// Serve dashboard static files if present
const dashboardDir = path_1.default.resolve(process.cwd(), '../dashboard/web/dist');
app.use('/dashboard', express_1.default.static(dashboardDir));
// Error handler
// eslint-disable-next-line @typescript-eslint/no-unused-vars
app.use((err, _req, res, _next) => {
    console.error(err);
    res.status(500).json({ error: 'Internal Server Error', message: err.message });
});
const port = Number(process.env.PORT || 8080);
app.listen(port, () => {
    console.log(`TrueCheck backend listening on http://localhost:${port}`);
});
