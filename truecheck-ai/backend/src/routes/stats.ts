import { Router } from 'express';
import { getRecentResults, getStats } from '../services/resultService';

const router = Router();

router.get('/recent', async (_req, res) => {
  const data = await getRecentResults(50);
  res.json({ data });
});

router.get('/stats', async (_req, res) => {
  const stats = await getStats();
  res.json(stats);
});

export default router;