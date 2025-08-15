class ScreeningPayload {
	final String deviceId;
	final String? userId;
	final String timestamp;
	final double? latitude;
	final double? longitude;
	final double riskScore;
	final String riskLabel;
	final Map<String, double> confidences;
	final String modelVersion;
	final String offlineId;
	final String? notes;

	ScreeningPayload({
		required this.deviceId,
		this.userId,
		required this.timestamp,
		this.latitude,
		this.longitude,
		required this.riskScore,
		required this.riskLabel,
		required this.confidences,
		required this.modelVersion,
		required this.offlineId,
		this.notes,
	});

	Map<String, dynamic> toJson() => {
		'deviceId': deviceId,
		'userId': userId,
		'timestamp': timestamp,
		'latitude': latitude,
		'longitude': longitude,
		'riskScore': riskScore,
		'riskLabel': riskLabel,
		'confidences': confidences,
		'modelVersion': modelVersion,
		'offlineId': offlineId,
		'notes': notes,
	};
}