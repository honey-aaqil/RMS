import './StatusBadge.css';

export default function StatusBadge({ status }) {
  const isActive = status === 'active';
  return (
    <span className={`status-badge ${isActive ? 'active' : 'inactive'}`}>
      <span className="status-dot" />
      {isActive ? 'Active' : 'Inactive'}
    </span>
  );
}
