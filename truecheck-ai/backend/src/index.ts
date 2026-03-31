import 'dotenv/config';
import express, { Request, Response, NextFunction } from 'express';
import cors from 'cors';
import morgan from 'morgan';
import mongoose from 'mongoose';
import path from 'path';
import analysisRouter from './routes/analysis';
import reportRouter from './routes/report';
import statsRouter from './routes/stats';

const app = express();

app.use(cors());
app.use(express.json({ limit: '20mb' }));
app.use(morgan('dev'));

const mongoUri = process.env.MONGODB_URI;
if (mongoUri) {
  mongoose
    .connect(mongoUri)
    .then(() => console.log('MongoDB connected'))
    .catch((err) => console.error('MongoDB connection error:', err));
} else {
  console.log('MONGODB_URI not set. Using in-memory store.');
}

app.get('/api/health', (_: Request, res: Response) => {
  res.json({ status: 'ok', service: 'truecheck-backend', time: new Date().toISOString() });
});

app.use('/api', analysisRouter);
app.use('/api', reportRouter);
app.use('/api', statsRouter);

// Serve dashboard static files if present
const dashboardDir = path.resolve(process.cwd(), '../dashboard/web/dist');
app.use('/dashboard', express.static(dashboardDir));

// Error handler
// eslint-disable-next-line @typescript-eslint/no-unused-vars
app.use((err: Error, _req: Request, res: Response, _next: NextFunction) => {
  console.error(err);
  res.status(500).json({ error: 'Internal Server Error', message: err.message });
});

const port = Number(process.env.PORT || 8080);
app.listen(port, () => {
  console.log(`TrueCheck backend listening on http://localhost:${port}`);
});