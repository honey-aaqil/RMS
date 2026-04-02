import './StatsCard.css';

export default function StatsCard({ icon, label, value, trend, trendUp, delay }) {
  return (
    <div className={`stats-card animate-fade-in-up ${delay ? `delay-${delay}` : ''}`}>
      <div className="stats-card-icon">{icon}</div>
      <div className="stats-card-content">
        <span className="stats-card-label">{label}</span>
        <span className="stats-card-value">{value}</span>
        {trend && (
          <span className={`stats-card-trend ${trendUp ? 'up' : 'down'}`}>
            {trendUp ? '↑' : '↓'} {trend}
          </span>
        )}
      </div>
    </div>
  );
}
