import { Link } from 'react-router-dom';
import './Landing.css';

export default function Landing() {
  return (
    <div className="landing">
      {/* Navigation */}
      <nav className="landing-nav">
        <div className="landing-nav-inner">
          <div className="landing-logo">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M14 2L24 8V20L14 26L4 20V8L14 2Z" stroke="#004BBD" strokeWidth="2" fill="none"/>
              <path d="M14 8L19 11V17L14 20L9 17V11L14 8Z" fill="#004BBD"/>
            </svg>
            <span>RecruiterMS</span>
          </div>
          <div className="landing-nav-links">
            <a href="#features">Features</a>
            <a href="#metrics">Metrics</a>
            <Link to="/login" className="landing-nav-login">Login</Link>
            <Link to="/signup" className="landing-nav-cta">Get Started</Link>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <section className="hero">
        <div className="hero-bg-orb hero-orb-1" />
        <div className="hero-bg-orb hero-orb-2" />
        <div className="hero-content">
          <div className="hero-badge animate-fade-in">
            <span className="hero-badge-dot" />
            Modern Talent Acquisition Platform
          </div>
          <h1 className="hero-title animate-fade-in-up delay-100">
            Recruit Smarter,<br />
            <span className="hero-title-accent">Not Harder.</span>
          </h1>
          <p className="hero-subtitle animate-fade-in-up delay-200">
            The executive-grade recruitment management system designed for modern talent teams. 
            Streamline your hiring workflow with intelligent automation and real-time analytics.
          </p>
          <div className="hero-actions animate-fade-in-up delay-300">
            <Link to="/signup" className="btn-primary-lg">
              Start Recruiting
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </Link>
            <Link to="/login" className="btn-ghost-lg">
              Sign In
            </Link>
          </div>
          <div className="hero-stats animate-fade-in-up delay-400">
            <div className="hero-stat">
              <span className="hero-stat-value">500+</span>
              <span className="hero-stat-label">Active Recruiters</span>
            </div>
            <div className="hero-stat-divider" />
            <div className="hero-stat">
              <span className="hero-stat-value">10K+</span>
              <span className="hero-stat-label">Placements Made</span>
            </div>
            <div className="hero-stat-divider" />
            <div className="hero-stat">
              <span className="hero-stat-value">98%</span>
              <span className="hero-stat-label">Satisfaction Rate</span>
            </div>
          </div>
        </div>
        <div className="hero-visual animate-scale-in delay-300">
          <div className="hero-dashboard-preview">
            <div className="preview-topbar">
              <div className="preview-dots">
                <span /><span /><span />
              </div>
              <span className="preview-url">recruiterms.app/dashboard</span>
            </div>
            <div className="preview-content">
              <div className="preview-sidebar">
                <div className="preview-sidebar-item active" />
                <div className="preview-sidebar-item" />
                <div className="preview-sidebar-item" />
              </div>
              <div className="preview-main">
                <div className="preview-cards">
                  <div className="preview-card" />
                  <div className="preview-card" />
                  <div className="preview-card" />
                </div>
                <div className="preview-table">
                  <div className="preview-row header" />
                  <div className="preview-row" />
                  <div className="preview-row" />
                  <div className="preview-row" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="features-section" id="features">
        <div className="section-header animate-fade-in-up">
          <span className="section-tag">Features</span>
          <h2 className="section-title">Everything you need to<br />manage recruitment</h2>
          <p className="section-subtitle">Built for modern talent acquisition teams who demand excellence.</p>
        </div>
        <div className="features-grid">
          <div className="feature-card animate-fade-in-up delay-100">
            <div className="feature-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </div>
            <h3 className="feature-title">Recruiter Profiles</h3>
            <p className="feature-desc">Complete recruiter management with company details, positions, contact info, and real-time status tracking.</p>
          </div>
          <div className="feature-card animate-fade-in-up delay-200">
            <div className="feature-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </div>
            <h3 className="feature-title">Secure Authentication</h3>
            <p className="feature-desc">JWT-based authentication with email verification, ensuring only verified users access the platform.</p>
          </div>
          <div className="feature-card animate-fade-in-up delay-300">
            <div className="feature-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
              </svg>
            </div>
            <h3 className="feature-title">Real-time Analytics</h3>
            <p className="feature-desc">Live dashboard with key metrics, activity tracking, and performance insights at your fingertips.</p>
          </div>
          <div className="feature-card animate-fade-in-up delay-400">
            <div className="feature-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
              </svg>
            </div>
            <h3 className="feature-title">Responsive Design</h3>
            <p className="feature-desc">Fully responsive PWA that works beautifully on desktop, tablet, and mobile devices.</p>
          </div>
        </div>
      </section>

      {/* Metrics Section */}
      <section className="metrics-section" id="metrics">
        <div className="section-header animate-fade-in-up">
          <span className="section-tag">By The Numbers</span>
          <h2 className="section-title">Trusted by recruitment<br />teams worldwide</h2>
        </div>
        <div className="metrics-grid">
          <div className="metric-card animate-fade-in-up delay-100">
            <span className="metric-value">3x</span>
            <span className="metric-label">Faster Hiring</span>
          </div>
          <div className="metric-card animate-fade-in-up delay-200">
            <span className="metric-value">60%</span>
            <span className="metric-label">Less Manual Work</span>
          </div>
          <div className="metric-card animate-fade-in-up delay-300">
            <span className="metric-value">24/7</span>
            <span className="metric-label">Platform Availability</span>
          </div>
          <div className="metric-card animate-fade-in-up delay-400">
            <span className="metric-value">100%</span>
            <span className="metric-label">Data Security</span>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="cta-section">
        <div className="cta-content animate-fade-in-up">
          <h2 className="cta-title">Ready to transform<br />your recruiting?</h2>
          <p className="cta-subtitle">Join hundreds of teams already using RecruiterMS to streamline their hiring process.</p>
          <Link to="/signup" className="btn-primary-lg">
            Get Started Free
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </Link>
        </div>
      </section>

      {/* Footer */}
      <footer className="landing-footer">
        <div className="footer-inner">
          <div className="footer-brand">
            <svg width="24" height="24" viewBox="0 0 28 28" fill="none">
              <path d="M14 2L24 8V20L14 26L4 20V8L14 2Z" stroke="#004BBD" strokeWidth="2" fill="none"/>
              <path d="M14 8L19 11V17L14 20L9 17V11L14 8Z" fill="#004BBD"/>
            </svg>
            <span>RecruiterMS</span>
          </div>
          <p className="footer-copy">© 2026 RecruiterMS. All rights reserved.</p>
        </div>
      </footer>
    </div>
  );
}
