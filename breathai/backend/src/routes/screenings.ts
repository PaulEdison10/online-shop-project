import { Router } from 'express';
import { createScreening, aggregateScreenings, stats } from '../controllers/screeningsController.js';

const router = Router();

router.post('/', createScreening);
router.get('/aggregate', aggregateScreenings);
router.get('/stats', stats);

export default router;