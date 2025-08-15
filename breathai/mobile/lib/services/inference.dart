class InferenceService {
	Future<Map<String, dynamic>> runOnWav(String wavPath) async {
		// TODO: Integrate tflite_flutter and mapping to output
		return {
			'label': 'Abnormal',
			'score': 0.72,
			'probs': {'Normal': 0.28, 'Abnormal': 0.72},
			'modelVersion': '1.0.0',
		};
	}
}