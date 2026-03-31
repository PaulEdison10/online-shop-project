import { Router } from 'express';
import multer from 'multer';
import { analyzeMediaController } from '../controllers/analysisController';

const router = Router();

const storage = multer.memoryStorage();
const upload = multer({ storage });

router.post(
	'/analyze',
	upload.single('file'),
	analyzeMediaController
);

export default router;