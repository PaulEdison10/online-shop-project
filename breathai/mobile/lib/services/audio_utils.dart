import 'dart:math' as math;

List<double> padOrTruncate(List<double> samples, int targetLength) {
	if (samples.length == targetLength) return samples;
	if (samples.length > targetLength) return samples.sublist(0, targetLength);
	final out = List<double>.from(samples);
	out.addAll(List<double>.filled(targetLength - samples.length, 0.0));
	return out;
}

List<double> normalizePeak(List<double> samples, {double target = 0.9}) {
	double peak = 1e-7;
	for (final s in samples) peak = math.max(peak, s.abs());
	final scale = target / peak;
	return samples.map((s) => s * scale).toList();
}