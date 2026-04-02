import { useAuth } from '../context/AuthContext';
import './TopNavbar.css';

export default function TopNavbar({ title, onSearch, searchValue }) {
  const { user } = useAuth();

  return (
    <header className="top-navbar">
      <div className="top-navbar-left">
        <h1 className="top-navbar-title">{title || 'Dashboard'}</h1>
      </div>
      <div className="top-navbar-center">
        {onSearch && (
          <div className="search-wrapper">
            <svg className="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
              type="text"
              className="search-input"
              placeholder="Search recruiters..."
              value={searchValue || ''}
              onChange={(e) => onSearch(e.target.value)}
            />
          </div>
        )}
      </div>
      <div className="top-navbar-right">
        <div className="navbar-user-pill">
          <div className="navbar-avatar">
            {user?.name?.charAt(0)?.toUpperCase() || 'U'}
          </div>
          <span className="navbar-username">{user?.name || 'User'}</span>
        </div>
      </div>
    </header>
  );
}
