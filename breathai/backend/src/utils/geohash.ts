import ngeohash from 'ngeohash';

export function geohashFor(lat?: number, lon?: number, precision: number = 6): string | undefined {
	if (typeof lat !== 'number' || typeof lon !== 'number') return undefined;
	return ngeohash.encode(lat, lon, precision);
}