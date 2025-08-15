import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';

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

	Future<void> _recordAndInfer() async {
		setState(() { _recording = true; _label = null; _score = null; });
		await Future.delayed(const Duration(seconds: 1));
		// TODO: integrate actual audio recording and TFLite inference
		// Placeholder result
		final label = 'Abnormal';
		final score = 0.72;

		final box = Hive.box('screenings');
		final offlineId = DateTime.now().millisecondsSinceEpoch.toString();
		final payload = {
			'deviceId': 'demo-device',
			'timestamp': DateTime.now().toUtc().toIso8601String(),
			'riskScore': score,
			'riskLabel': label,
			'confidences': {'Normal': 1 - score, 'Abnormal': score},
			'modelVersion': '1.0.0',
			'offlineId': offlineId,
		};
		await box.put(offlineId, payload);
		setState(() { _recording = false; _label = label; _score = score; });
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
								child: Text('Result: $_label (${((_score ?? 0)*100).toStringAsFixed(1)}%)'),
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
											final s = items[idx] as Map;
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