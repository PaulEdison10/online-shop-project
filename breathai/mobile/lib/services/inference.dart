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

	Future<void> init() async {
		// Load model
		_interpreter = await Interpreter.fromAsset('assets/model/breathai.tflite');
		// Load label map
		final lmStr = await rootBundle.loadString('assets/model/label_map.json');
		_labelMap = Map<String, int>.from(jsonDecode(lmStr));
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
		if (_interpreter == null) await init();
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