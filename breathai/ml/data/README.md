# Data Directory

Place your dataset here. Expected structure:

```
ml/data/
  Normal/
    sample_001.wav
    sample_002.wav
  Abnormal/
    sample_003.wav
    ...
```

For a quick smoke test, generate synthetic data:
```bash
python data/generate_synthetic.py --out ./data/synth --num_per_class 40 --sr 16000 --duration 2.0
```

This produces:
```
ml/data/synth/
  Normal/*.wav
  Abnormal/*.wav
```