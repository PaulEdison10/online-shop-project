import L from 'leaflet';

const API_BASE = window.location.origin.replace(/\/dashboard.*/, '');

async function fetchJson(path) {
	const res = await fetch(`${API_BASE}${path}`);
	return await res.json();
}

function riskToColor(risk) {
	// green->yellow->red
	const r = Math.round(255 * Math.min(Math.max((risk - 0.5) * 2, 0), 1));
	const g = Math.round(255 * Math.min(Math.max((1 - risk) * 2, 0), 1));
	return `rgb(${r},${g},0)`;
}

function initMap() {
	const map = L.map('map').setView([20.5937, 78.9629], 5);
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '© OpenStreetMap'
	}).addTo(map);
	return map;
}

async function render() {
	const map = initMap();
	const stats = await fetchJson('/api/screenings/stats');
	document.getElementById('total').textContent = stats.total;
	document.getElementById('last24h').textContent = stats.last24h;
	document.getElementById('abRate').textContent = (stats.abnormalRate * 100).toFixed(1) + '%';

	const buckets = await fetchJson('/api/screenings/aggregate?precision=6&sinceDays=30');
	buckets.forEach(b => {
		if (!b.lat || !b.lon) return;
		L.circleMarker([b.lat, b.lon], {
			radius: Math.max(4, Math.min(18, Math.sqrt(b.count))),
			color: riskToColor(b.avgRisk),
			fillOpacity: 0.6
		}).addTo(map).bindPopup(`Count: ${b.count}<br/>Avg risk: ${b.avgRisk.toFixed(2)}<br/>Geohash: ${b.geohash}`);
	});
}

render();