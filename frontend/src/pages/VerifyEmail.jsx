import { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import api from '../api/axios';
import './VerifyEmail.css';

export default function VerifyEmail() {
  const [searchParams] = useSearchParams();
  const token = searchParams.get('token');
  const [status, setStatus] = useState('loading'); // loading | success | expired | error
  const [message, setMessage] = useState('');
  const [resendEmail, setResendEmail] = useState('');
  const [resendLoading, setResendLoading] = useState(false);
  const [resendMessage, setResendMessage] = useState('');

  useEffect(() => {
    if (!token) {
      setStatus('error');
      setMessage('No verification token provided.');
      return;
    }

    const verify = async () => {
      try {
        const res = await api.get(`/api/auth/verify-email?token=${token}`);
        if (res.data.success) {
          if (res.data.code === 'ALREADY_VERIFIED') {
            setStatus('success');
            setMessage('Email is already verified. You can login.');
          } else {
            setStatus('success');
            setMessage('Email verified successfully! You can now login.');
          }
        }
      } catch (err) {
        const data = err.response?.data;
        if (data?.code === 'TOKEN_EXPIRED') {
          setStatus('expired');
          setMessage('Verification link has expired. Please request a new one.');
        } else if (data?.code === 'INVALID_TOKEN') {
          setStatus('error');
          setMessage('Invalid verification link.');
        } else {
          setStatus('error');
          setMessage(data?.message || 'Verification failed. Please try again.');
        }
      }
    };

    verify();
  }, [token]);

  const handleResend = async (e) => {
    e.preventDefault();
    if (!resendEmail) return;
    setResendLoading(true);
    setResendMessage('');

    try {
      const res = await api.post('/api/auth/resend-verification', { email: resendEmail });
      setResendMessage(res.data.message || 'Verification email sent. Please check your inbox.');
    } catch (err) {
      setResendMessage(err.response?.data?.message || 'Failed to resend. Please try again.');
    } finally {
      setResendLoading(false);
    }
  };

  return (
    <div className="verify-page">
      <div className="auth-bg-orb auth-orb-1" />
      <div className="auth-bg-orb auth-orb-2" />

      <div className="verify-card animate-scale-in">
        {/* Loading State */}
        {status === 'loading' && (
          <div className="verify-state">
            <div className="verify-spinner">
              <div className="spinner-ring" />
            </div>
            <h2 className="verify-title">Verifying your email…</h2>
            <p className="verify-desc">Please wait while we confirm your email address.</p>
          </div>
        )}

        {/* Success State */}
        {status === 'success' && (
          <div className="verify-state animate-fade-in">
            <div className="verify-icon success">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="checkmark-svg">
                <circle cx="12" cy="12" r="10" />
                <path d="M9 12l2 2 4-4" className="checkmark-path" />
              </svg>
            </div>
            <h2 className="verify-title">Email Verified!</h2>
            <p className="verify-desc">{message}</p>
            <Link to="/login" className="btn-primary-full verify-btn">
              Go to Login
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </Link>
          </div>
        )}

        {/* Expired State */}
        {status === 'expired' && (
          <div className="verify-state animate-fade-in">
            <div className="verify-icon expired">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
            </div>
            <h2 className="verify-title">Link Expired</h2>
            <p className="verify-desc">{message}</p>
            
            <form className="resend-form" onSubmit={handleResend}>
              <div className="input-wrapper">
                <svg className="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                <input
                  type="email"
                  className="form-input"
                  placeholder="Enter your email"
                  value={resendEmail}
                  onChange={(e) => setResendEmail(e.target.value)}
                  required
                />
              </div>
              <button type="submit" className="btn-primary-full verify-btn" disabled={resendLoading}>
                {resendLoading ? <div className="btn-spinner" /> : 'Resend Verification'}
              </button>
            </form>
            {resendMessage && (
              <p className="resend-msg animate-fade-in">{resendMessage}</p>
            )}
          </div>
        )}

        {/* Error State */}
        {status === 'error' && (
          <div className="verify-state animate-fade-in">
            <div className="verify-icon error">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
              </svg>
            </div>
            <h2 className="verify-title">Verification Failed</h2>
            <p className="verify-desc">{message}</p>
            <Link to="/signup" className="btn-primary-full verify-btn">
              Back to Sign Up
            </Link>
          </div>
        )}
      </div>
    </div>
  );
}
