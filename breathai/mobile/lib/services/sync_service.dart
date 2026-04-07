import 'dart:convert';

import 'package:hive/hive.dart';
import 'package:http/http.dart' as http;

class SyncService {
	final String apiBase;
	SyncService(this.apiBase);

	Future<void> sync() async {
		final box = Hive.box('screenings');
		final keys = box.keys.toList();
		for (final k in keys) {
			final payload = Map<String, dynamic>.from(box.get(k));
			final ok = await _post('/api/screenings', payload);
			if (ok) {
				await box.delete(k);
			}
		}
	}

	Future<bool> _post(String path, Map<String, dynamic> body) async {
		try {
			final res = await http.post(Uri.parse('$apiBase$path'),
				headers: {'Content-Type': 'application/json'},
				body: jsonEncode(body));
			return res.statusCode == 201 || res.statusCode == 409;
		} catch (_) {
			return false;
		}
	}
}