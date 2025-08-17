import 'dart:convert';
import 'dart:io';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

void main() {
	runApp(const TrueCheckApp());
}

class TrueCheckApp extends StatelessWidget {
	const TrueCheckApp({super.key});
	@override
	Widget build(BuildContext context) {
		return MaterialApp(
			title: 'TrueCheck AI',
			debugShowCheckedModeBanner: false,
			home: const HomePage(),
			theme: ThemeData(colorSchemeSeed: Colors.indigo, useMaterial3: true),
		);
	}
}

class HomePage extends StatefulWidget {
	const HomePage({super.key});
	@override
	State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
	String _status = 'Idle';
	Map<String, dynamic>? _result;
	bool _loading = false;

	Future<void> _pickAndAnalyze() async {
		setState(() { _result = null; _status = 'Selecting file…'; });
		final res = await FilePicker.platform.pickFiles(withData: false);
		if (res == null || res.files.isEmpty) return;
		final f = res.files.first;
		final path = f.path;
		if (path == null) return;
		final file = File(path);
		final mediaType = _inferMediaType(f.extension ?? '');
		await _analyzeFile(file, mediaType);
	}

	String _inferMediaType(String ext) {
		final e = ext.toLowerCase();
		if (['jpg','jpeg','png','webp','bmp'].contains(e)) return 'image';
		if (['mp4','mov','avi','mkv'].contains(e)) return 'video';
		if (['wav','mp3','m4a','aac','flac','ogg'].contains(e)) return 'audio';
		return 'image';
	}

	Future<void> _analyzeFile(File file, String mediaType) async {
		setState(() { _loading = true; _status = 'Uploading…'; });
		final uri = Uri.parse(const String.fromEnvironment('BACKEND_URL', defaultValue: 'http://10.0.2.2:8080') + '/api/analyze?mediaType=' + mediaType);
		final req = http.MultipartRequest('POST', uri);
		req.files.add(await http.MultipartFile.fromPath('file', file.path));
		final streamed = await req.send();
		final body = await streamed.stream.bytesToString();
		final code = streamed.statusCode;
		if (code == 200) {
			final data = jsonDecode(body);
			setState(() { _result = data; _status = 'Done'; });
		} else {
			setState(() { _status = 'Error: ' + body; });
		}
		setState(() { _loading = false; });
	}

	@override
	Widget build(BuildContext context) {
		return Scaffold(
			appBar: AppBar(title: const Text('TrueCheck AI')),
			body: Padding(
				padding: const EdgeInsets.all(16),
				child: Column(
					crossAxisAlignment: CrossAxisAlignment.stretch,
					children: [
						FilledButton.icon(
							onPressed: _loading ? null : _pickAndAnalyze,
							icon: const Icon(Icons.upload_file),
							label: const Text('Upload media for analysis'),
						),
						const SizedBox(height: 12),
						Text(_status),
						const SizedBox(height: 12),
						if (_loading) const LinearProgressIndicator(),
						if (_result != null) ResultCard(result: _result!),
					],
				),
			),
		);
	}
}

class ResultCard extends StatelessWidget {
	final Map<String, dynamic> result;
	const ResultCard({super.key, required this.result});
	Color _color(String label) {
		switch(label){
			case 'real': return Colors.green;
			case 'fake': return Colors.red;
			default: return Colors.orange;
		}
	}
	@override
	Widget build(BuildContext context) {
		final label = (result['label'] ?? 'suspicious') as String;
		final confidence = (result['confidence'] ?? 0.0) as num;
		return Card(
			child: Padding(
				padding: const EdgeInsets.all(16),
				child: Column(
					crossAxisAlignment: CrossAxisAlignment.start,
					children: [
						Text('Result', style: Theme.of(context).textTheme.titleLarge),
						const SizedBox(height: 8),
						Row(
							children: [
								Container(width: 12, height: 12, decoration: BoxDecoration(color: _color(label), shape: BoxShape.circle)),
								const SizedBox(width: 8),
								Text(label.toUpperCase(), style: TextStyle(fontWeight: FontWeight.bold, color: _color(label))),
								const Spacer(),
								Text('${(confidence*100).toStringAsFixed(1)}%'),
							],
						),
						const SizedBox(height: 12),
						FilledButton(
							onPressed: (){
								final id = result['id'];
								final url = '${const String.fromEnvironment('BACKEND_URL', defaultValue: 'http://10.0.2.2:8080')}/api/report?id=' + (id?.toString() ?? '') + '&label=' + label + '&confidence=' + confidence.toString();
								showDialog(context: context, builder: (_) => AlertDialog(title: const Text('Report URL'), content: Text(url)));
							},
							child: const Text('Generate Report'),
						)
					],
				),
			),
		);
	}
}