import 'dart:convert';
import 'dart:typed_data';

import 'package:flutter/services.dart' show rootBundle;
import 'package:tflite_flutter/tflite_flutter.dart';

class InferenceService {
	Interpreter? _interpreter;
	late Map<String, int> _labelMap;
	late Map<int, String> _invLabelMap;
	late int _sampleRate;
	late double _duration;
	bool _fallback = false;

	Future<void> init() async {
		try {
			_interpreter = await Interpreter.fromAsset('assets/model/breathai.tflite');
		} catch (_) {
			_fallback = true;
		}
		// Load label map or default
		try {
			final lmStr = await rootBundle.loadString('assets/model/label_map.json');
			_labelMap = Map<String, int>.from(jsonDecode(lmStr));
		} catch (_) {
			_labelMap = { 'Normal': 0, 'Abnormal': 1 };
		}
		_invLabelMap = {for (final e in _labelMap.entries) e.value: e.key};
		// Load metadata (optional); fallback to defaults
		try {
			final metaStr = await rootBundle.loadString('assets/model/metadata.json');
			final meta = jsonDecode(metaStr);
			_sampleRate = (meta['sample_rate'] as num?)?.toInt() ?? 16000;
			_duration = (meta['duration_seconds'] as num?)?.toDouble() ?? 2.0;
		} catch (_) {
			_sampleRate = 16000;
			_duration = 2.0;
		}
	}

	Future<Map<String, dynamic>> runOnPcmFloat(List<double> samples) async {
		if (_interpreter == null && !_fallback) await init();
		if (_fallback || _interpreter == null) {
			return _heuristicInference(samples);
		}
		try {
			final interpreter = _interpreter!;
			final inputLength = (_sampleRate * _duration).toInt();
			final padded = _padOrTruncate(samples, inputLength);
			final input = List.generate(1, (_) => List.generate(inputLength, (i) => [padded[i]]));
			final output = List.generate(1, (_) => List.filled(_labelMap.length, 0.0));
			interpreter.run(input, output);
			final probs = output[0];
			int predIdx = 0;
			double best = -1;
			for (int i = 0; i < probs.length; i++) {
				if (probs[i] > best) { best = probs[i]; predIdx = i; }
			}
			return {
				'label': _invLabelMap[predIdx] ?? '$predIdx',
				'score': best,
				'probs': { for (int i = 0; i < probs.length; i++) _invLabelMap[i] ?? '$i': probs[i] },
				'modelVersion': '1.0.0',
			};
		} catch (_) {
			return _heuristicInference(samples);
		}
	}

	Map<String, dynamic> _heuristicInference(List<double> samples) {
		// Simple RMS-based risk proxy; for demo if model asset missing
		double sumSq = 0;
		for (final s in samples) { sumSq += s * s; }
		final rms = samples.isNotEmpty ? (sumSq / samples.length).sqrt() : 0.0;
		// Map RMS 0.02..0.2 -> 0..1
		final score = rms <= 0.02 ? 0.1 : (rms >= 0.2 ? 0.9 : (rms - 0.02) / (0.2 - 0.02));
		final label = score > 0.5 ? 'Abnormal' : 'Normal';
		return {
			'label': label,
			'score': score,
			'probs': { 'Normal': 1 - score, 'Abnormal': score },
			'modelVersion': 'heuristic-0.1',
		};
	}

	extension on double {
		double sqrt() => MathSqrt.sqrt(this);
	}

class MathSqrt { static double sqrt(double x) => x <= 0 ? 0 : x.toDouble().toString() == 'NaN' ? 0 : _sqrtNewton(x); }

double _sqrtNewton(double x) {
	double r = x;
	for (int i = 0; i < 10; i++) { r = 0.5 * (r + x / r); }
	return r;
}

	List<double> _padOrTruncate(List<double> s, int n) {
		if (s.length == n) return s;
		if (s.length > n) return s.sublist(0, n);
		return s + List.filled(n - s.length, 0.0);
	}

	void dispose() {
		_interpreter?.close();
	}
}