import { Request, Response } from 'express';
import Screening from '../models/Screening.js';
import { geohashFor } from '../utils/geohash.js';

export async function createScreening(req: Request, res: Response) {
	try {
		const body = req.body || {};
		const { deviceId, timestamp, latitude, longitude, riskScore, riskLabel, confidences, modelVersion, offlineId, notes } = body;
		if (!deviceId || !timestamp || typeof riskScore !== 'number' || !riskLabel || !confidences || !modelVersion || !offlineId) {
			return res.status(400).json({ error: 'Missing required fields' });
		}
		const geohash = geohashFor(latitude, longitude, 6);
		const doc = await Screening.findOneAndUpdate(
			{ offlineId },
			{ $setOnInsert: { deviceId, userId: body.userId, timestamp: new Date(timestamp), latitude, longitude, geohash, riskScore, riskLabel, confidences, modelVersion, offlineId, notes } },
			{ upsert: true, new: true }
		);
		return res.status(201).json({ id: doc._id, status: 'ok' });
	} catch (err: any) {
		if (err.code === 11000) {
			return res.status(409).json({ error: 'Duplicate offlineId' });
		}
		console.error(err);
		return res.status(500).json({ error: 'Server error' });
	}
}

export async function aggregateScreenings(req: Request, res: Response) {
	try {
		const precision = Math.min(Math.max(parseInt(String(req.query.precision || '6')), 1), 9);
		const sinceDays = Math.min(Math.max(parseInt(String(req.query.sinceDays || '30')), 1), 365);
		const since = new Date(Date.now() - sinceDays * 24 * 3600 * 1000);

		const pipeline = [
			{ $match: { timestamp: { $gte: since } } },
			{ $group: {
				_id: '$geohash',
				count: { $sum: 1 },
				avgRisk: { $avg: '$riskScore' },
				lat: { $avg: '$latitude' },
				lon: { $avg: '$longitude' },
			}},
			{ $project: { _id: 0, geohash: '$_id', count: 1, avgRisk: 1, lat: 1, lon: 1 } },
			{ $sort: { count: -1 } }
		];

		const results = await Screening.aggregate(pipeline);
		res.json(results);
	} catch (err) {
		console.error(err);
		res.status(500).json({ error: 'Server error' });
	}
}

export async function stats(req: Request, res: Response) {
	try {
		const now = new Date();
		const since24h = new Date(now.getTime() - 24 * 3600 * 1000);
		const total = await Screening.countDocuments();
		const last24h = await Screening.countDocuments({ timestamp: { $gte: since24h } });
		const abnormal = await Screening.countDocuments({ riskLabel: { $ne: 'Normal' } });
		const abnormalRate = total > 0 ? abnormal / total : 0;
		res.json({ total, last24h, abnormalRate });
	} catch (err) {
		console.error(err);
		res.status(500).json({ error: 'Server error' });
	}
}