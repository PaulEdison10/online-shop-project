import 'dart:io';
import 'dart:typed_data';

import 'package:permission_handler/permission_handler.dart';
import 'package:record/record.dart';
import 'dart:async'; // Added missing import for StreamController

class AudioRecorderService {
	final _record = AudioRecorder();

	Future<List<double>> recordPcmFloats({int seconds = 10}) async {
		if (!await _ensurePermission()) {
			throw Exception('Microphone permission denied');
		}
		final config = RecordConfig(
			audioEncoder: AudioEncoder.pcm16bits,
			sampleRate: 16000,
			numChannels: 1,
			enableNoiseSuppressor: false,
		);
		final path = await _record.startStream(config);
		final completer = StreamController<List<double>>();
		final samples = <double>[];
		final sub = path.listen((data) {
			final bytes = data;
			// PCM16 little-endian to floats [-1,1]
			for (int i = 0; i < bytes.length; i += 2) {
				final int16 = bytes[i] | (bytes[i + 1] << 8);
				double v = int16.toSigned(16) / 32768.0;
				samples.add(v);
			}
		});
		await Future.delayed(Duration(seconds: seconds));
		await _record.stop();
		await sub.cancel();
		return samples;
	}

	Future<bool> _ensurePermission() async {
		final mic = await Permission.microphone.request();
		return mic.isGranted;
	}
}