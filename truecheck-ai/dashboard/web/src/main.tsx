import React from 'react'
import { createRoot } from 'react-dom/client'

function useStats() {
	const [data, setData] = React.useState<{total:number, byLabel:{real:number, fake:number, suspicious:number}}|null>(null)
	React.useEffect(() => {
		fetch('/api/stats').then(r=>r.json()).then(setData).catch(()=>{})
	}, [])
	return data
}

function App() {
	const stats = useStats()
	return (
		<div style={{fontFamily:'system-ui, -apple-system, Segoe UI, Roboto, sans-serif', padding: 24}}>
			<h1>TrueCheck Dashboard</h1>
			{!stats ? <p>Loading…</p> : (
				<div>
					<p>Total analyses: <b>{stats.total}</b></p>
					<div style={{display:'flex', gap: 16}}>
						<Card title="Real" value={stats.byLabel.real} color="#16a34a" />
						<Card title="Suspicious" value={stats.byLabel.suspicious} color="#f59e0b" />
						<Card title="Fake" value={stats.byLabel.fake} color="#dc2626" />
					</div>
				</div>
			)}
			<h2 style={{marginTop: 24}}>Recent</h2>
			<RecentTable />
		</div>
	)
}

function Card({title, value, color}:{title:string, value:number, color:string}){
	return (
		<div style={{border:'1px solid #e5e7eb', borderRadius:12, padding:16, minWidth:160}}>
			<div style={{fontSize:12, color:'#6b7280'}}>{title}</div>
			<div style={{fontSize:28, fontWeight:700, color}}>{value}</div>
		</div>
	)
}

function RecentTable(){
	const [rows, setRows] = React.useState<any[]>([])
	React.useEffect(()=>{ fetch('/api/recent').then(r=>r.json()).then(d=>setRows(d.data||[])).catch(()=>{}) },[])
	return (
		<table style={{width:'100%', borderCollapse:'collapse'}}>
			<thead>
				<tr>
					<th style={th}>When</th>
					<th style={th}>Type</th>
					<th style={th}>Label</th>
					<th style={th}>Confidence</th>
				</tr>
			</thead>
			<tbody>
				{rows.map((r,i)=> (
					<tr key={i}>
						<td style={td}>{new Date(r.createdAt).toLocaleString()}</td>
						<td style={td}>{r.mediaType}</td>
						<td style={td}>{r.resultLabel}</td>
						<td style={td}>{(r.confidence*100).toFixed(1)}%</td>
					</tr>
				))}
			</tbody>
		</table>
	)
}

const th: React.CSSProperties = { textAlign:'left', borderBottom:'1px solid #e5e7eb', padding:'8px 6px'}
const td: React.CSSProperties = { borderBottom:'1px solid #f3f4f6', padding:'8px 6px'}

const root = createRoot(document.getElementById('root')!)
root.render(<App />)