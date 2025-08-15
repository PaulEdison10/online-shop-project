import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:uuid/uuid.dart';

import 'models/screening.dart';
import 'services/audio_recorder.dart';
import 'services/inference.dart';
import 'services/sync_service.dart';

void main() async {
	WidgetsFlutterBinding.ensureInitialized();
	await Hive.initFlutter();
	await Hive.openBox('screenings');
	runApp(const BreathAIApp());
}

class BreathAIApp extends StatelessWidget {
	const BreathAIApp({super.key});
	@override
	Widget build(BuildContext context) {
		return MaterialApp(
			title: 'BreathAI',
			home: const HomePage(),
		);
	}
}

class HomePage extends StatefulWidget {
	const HomePage({super.key});
	@override
	State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
	bool _recording = false;
	String? _label;
	double? _score;
	final _rec = AudioRecorderService();
	final _infer = InferenceService();
	final _sync = SyncService('http://10.0.2.2:8080'); // Android emulator loopback

	Future<void> _recordAndInfer() async {
		setState(() { _recording = true; _label = null; _score = null; });
		try {
			final pcm = await _rec.recordPcmFloats(seconds: 10);
			await _infer.init();
			final result = await _infer.runOnPcmFloat(pcm);
			final pos = await _getPositionOrNull();
			final payload = ScreeningPayload(
				deviceId: 'demo-device',
				timestamp: DateTime.now().toUtc().toIso8601String(),
				latitude: pos?.latitude,
				longitude: pos?.longitude,
				riskScore: (result['score'] as num).toDouble(),
				riskLabel: result['label'] as String,
				confidences: Map<String, double>.from((result['probs'] as Map).map((k, v) => MapEntry(k as String, (v as num).toDouble()))),
				modelVersion: result['modelVersion'] as String,
				offlineId: const Uuid().v4(),
			);
			final box = Hive.box('screenings');
			await box.put(payload.offlineId, payload.toJson());
			await _sync.sync();
			setState(() { _label = payload.riskLabel; _score = payload.riskScore; });
		} catch (e) {
			setState(() { _label = 'Error'; _score = 0; });
		} finally {
			setState(() { _recording = false; });
		}
	}

	Future<Position?> _getPositionOrNull() async {
		try {
			final perm = await Geolocator.requestPermission();
			if (perm == LocationPermission.denied || perm == LocationPermission.deniedForever) return null;
			return await Geolocator.getCurrentPosition();
		} catch (_) {
			return null;
		}
	}

	@override
	Widget build(BuildContext context) {
		return Scaffold(
			appBar: AppBar(title: const Text('BreathAI')),
			body: Padding(
				padding: const EdgeInsets.all(16),
				child: Column(
					crossAxisAlignment: CrossAxisAlignment.stretch,
					children: [
						FilledButton(
							onPressed: _recording ? null : _recordAndInfer,
							child: Text(_recording ? 'Recording...' : 'Record Breath (10s)')),
						const SizedBox(height: 16),
						if (_label != null) Card(
							child: Padding(
								padding: const EdgeInsets.all(16),
								child: Text('Result: $_label ${_score != null ? '(${((_score ?? 0)*100).toStringAsFixed(1)}%)' : ''}'),
							),
						),
						const SizedBox(height: 16),
						Expanded(
							child: ValueListenableBuilder(
								valueListenable: Hive.box('screenings').listenable(),
								builder: (context, box, _) {
									final items = box.values.toList().reversed.toList();
									return ListView.builder(
										itemCount: items.length,
										itemBuilder: (context, idx) {
											final s = Map<String, dynamic>.from(items[idx]);
											return ListTile(
												title: Text('${s['riskLabel']} ${(s['riskScore']*100).toStringAsFixed(0)}%'),
												subtitle: Text(s['timestamp']),
											);
										},
									);
								},
							),
						)
					],
				),
			),
		);
	}
}