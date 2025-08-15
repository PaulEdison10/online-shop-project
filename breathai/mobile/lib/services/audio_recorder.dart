class AudioRecorderService {
	Future<String> recordWav({int seconds = 10}) async {
		// TODO: Integrate an actual audio recorder plugin (e.g., record)
		// Return path to WAV file recorded at 16 kHz mono
		await Future.delayed(Duration(seconds: seconds));
		throw UnimplementedError('Recorder integration pending');
	}
}