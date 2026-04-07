import express from 'express';
import morgan from 'morgan';
import cors from 'cors';
import dotenv from 'dotenv';
import mongoose from 'mongoose';
import path from 'path';
import screeningsRouter from './routes/screenings.js';

dotenv.config();

const app = express();

// Middleware
app.use(express.json({ limit: '2mb' }));
app.use(morgan('dev'));

const corsOrigins = (process.env.CORS_ORIGINS || '').split(',').map(s => s.trim()).filter(Boolean);
app.use(cors({ origin: corsOrigins.length ? corsOrigins : true }));

// MongoDB
const mongoUri = process.env.MONGO_URI || 'mongodb://localhost:27017/breathai';
mongoose.connect(mongoUri)
	.then(() => console.log('Connected to MongoDB'))
	.catch(err => console.error('MongoDB connection error', err));

// Health
app.get('/healthz', (_req, res) => {
	res.json({ status: 'ok' });
});

// API routes
app.use('/api/screenings', screeningsRouter);

// Dashboard static (built-in minimal client)
app.use('/dashboard', express.static(path.join(process.cwd(), 'public-dashboard')));

export default app;