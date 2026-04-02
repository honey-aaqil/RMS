import { useState, useEffect, useCallback } from 'react';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';
import Sidebar from '../components/Sidebar';
import BottomTabBar from '../components/BottomTabBar';
import TopNavbar from '../components/TopNavbar';
import StatsCard from '../components/StatsCard';
import StatusBadge from '../components/StatusBadge';
import './Home.css';

export default function Home() {
  const { user } = useAuth();
  const [recruiters, setRecruiters] = useState([]);
  const [pagination, setPagination] = useState({ page: 1, limit: 10, total: 0, totalPages: 0 });
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [loading, setLoading] = useState(true);
  const [editModal, setEditModal] = useState(null);
  const [deleteModal, setDeleteModal] = useState(null);
  const [addModal, setAddModal] = useState(false);
  const [formData, setFormData] = useState({ company_name: '', position: '', phone: '', status: 'active' });
  const [formLoading, setFormLoading] = useState(false);
  const [formError, setFormError] = useState('');

  const fetchRecruiters = useCallback(async (page = 1) => {
    setLoading(true);
    try {
      const params = new URLSearchParams();
      params.append('page', page);
      params.append('limit', pagination.limit);
      if (search) params.append('search', search);
      if (statusFilter) params.append('status', statusFilter);

      const res = await api.get(`/api/recruiters?${params.toString()}`);
      if (res.data.success) {
        setRecruiters(res.data.data || []);
        setPagination(res.data.pagination || { page: 1, limit: 10, total: 0, totalPages: 0 });
      }
    } catch (err) {
      console.error('Failed to fetch recruiters:', err);
    } finally {
      setLoading(false);
    }
  }, [search, statusFilter, pagination.limit]);

  useEffect(() => {
    fetchRecruiters();
  }, [fetchRecruiters]);

  const handleSearch = (val) => {
    setSearch(val);
  };

  const handleEdit = (recruiter) => {
    setEditModal(recruiter);
    setFormData({
      company_name: recruiter.company_name || '',
      position: recruiter.position || '',
      phone: recruiter.phone || '',
      status: recruiter.status || 'active',
    });
    setFormError('');
  };

  const handleEditSubmit = async (e) => {
    e.preventDefault();
    setFormLoading(true);
    setFormError('');
    try {
      await api.put(`/api/recruiters/${editModal.id}`, formData);
      setEditModal(null);
      fetchRecruiters(pagination.page);
    } catch (err) {
      setFormError(err.response?.data?.message || 'Update failed.');
    } finally {
      setFormLoading(false);
    }
  };

  const handleDelete = async () => {
    setFormLoading(true);
    try {
      await api.delete(`/api/recruiters/${deleteModal.id}`);
      setDeleteModal(null);
      fetchRecruiters(pagination.page);
    } catch (err) {
      setFormError(err.response?.data?.message || 'Delete failed.');
    } finally {
      setFormLoading(false);
    }
  };

  const handleAddSubmit = async (e) => {
    e.preventDefault();
    setFormLoading(true);
    setFormError('');
    try {
      await api.post('/api/recruiters', {
        user_id: user?.id,
        company_name: formData.company_name,
        position: formData.position,
        phone: formData.phone,
      });
      setAddModal(false);
      setFormData({ company_name: '', position: '', phone: '', status: 'active' });
      fetchRecruiters(1);
    } catch (err) {
      setFormError(err.response?.data?.message || 'Failed to create recruiter.');
    } finally {
      setFormLoading(false);
    }
  };

  const activeCount = recruiters.filter(r => r.status === 'active').length;
  const inactiveCount = recruiters.filter(r => r.status === 'inactive').length;

  return (
    <div className="dashboard-layout">
      <Sidebar />
      <div className="dashboard-main">
        <TopNavbar title="Dashboard" onSearch={handleSearch} searchValue={search} />

        <div className="dashboard-content">
          {/* Stats Row */}
          <div className="stats-grid">
            <StatsCard
              icon={
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              }
              label="Total Recruiters"
              value={pagination.total || recruiters.length}
              delay="100"
            />
            <StatsCard
              icon={
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                  <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
              }
              label="Active"
              value={activeCount}
              delay="200"
            />
            <StatsCard
              icon={
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="15" y1="9" x2="9" y2="15"/>
                  <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
              }
              label="Inactive"
              value={inactiveCount}
              delay="300"
            />
          </div>

          {/* Table Header */}
          <div className="table-header animate-fade-in delay-400">
            <h2 className="table-title">Recruiters</h2>
            <div className="table-actions">
              <select
                className="filter-select"
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
              >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <button className="btn-add" onClick={() => { setAddModal(true); setFormData({ company_name: '', position: '', phone: '', status: 'active' }); setFormError(''); }}>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Recruiter
              </button>
            </div>
          </div>

          {/* Table */}
          <div className="table-container animate-fade-in delay-500">
            {loading ? (
              <div className="table-loading">
                <div className="spinner-ring" />
              </div>
            ) : recruiters.length === 0 ? (
              <div className="table-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <line x1="19" y1="10" x2="19" y2="16"/>
                  <line x1="22" y1="13" x2="16" y2="13"/>
                </svg>
                <p>No recruiters found</p>
              </div>
            ) : (
              <div className="table-scroll">
                <table className="data-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Company</th>
                      <th>Position</th>
                      <th>Phone</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {recruiters.map((r, i) => (
                      <tr key={r.id} style={{ animationDelay: `${i * 50}ms` }} className="animate-fade-in">
                        <td>
                          <div className="cell-user">
                            <div className="cell-avatar">{r.name?.charAt(0)?.toUpperCase() || '?'}</div>
                            <span>{r.name}</span>
                          </div>
                        </td>
                        <td className="cell-muted">{r.email}</td>
                        <td>{r.company_name}</td>
                        <td>{r.position}</td>
                        <td className="cell-muted">{r.phone || '—'}</td>
                        <td><StatusBadge status={r.status} /></td>
                        <td>
                          <div className="cell-actions">
                            <button className="action-btn edit" onClick={() => handleEdit(r)} title="Edit">
                              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                              </svg>
                            </button>
                            {r.status === 'active' && (
                              <button className="action-btn delete" onClick={() => { setDeleteModal(r); setFormError(''); }} title="Deactivate">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                                  <polyline points="3 6 5 6 21 6"/>
                                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>

          {/* Pagination */}
          {pagination.totalPages > 1 && (
            <div className="pagination">
              <button
                className="pagination-btn"
                disabled={pagination.page <= 1}
                onClick={() => fetchRecruiters(pagination.page - 1)}
              >
                ← Previous
              </button>
              <span className="pagination-info">
                Page {pagination.page} of {pagination.totalPages}
              </span>
              <button
                className="pagination-btn"
                disabled={pagination.page >= pagination.totalPages}
                onClick={() => fetchRecruiters(pagination.page + 1)}
              >
                Next →
              </button>
            </div>
          )}
        </div>

        <BottomTabBar />
      </div>

      {/* Edit Modal */}
      {editModal && (
        <div className="modal-overlay" onClick={() => setEditModal(null)}>
          <div className="modal-card animate-scale-in" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>Edit Recruiter</h3>
              <button className="modal-close" onClick={() => setEditModal(null)}>×</button>
            </div>
            <form onSubmit={handleEditSubmit}>
              {formError && <div className="auth-error">{formError}</div>}
              <div className="form-group">
                <label className="form-label">Company Name</label>
                <input className="form-input modal-input" value={formData.company_name} onChange={(e) => setFormData({ ...formData, company_name: e.target.value })} required />
              </div>
              <div className="form-group">
                <label className="form-label">Position</label>
                <input className="form-input modal-input" value={formData.position} onChange={(e) => setFormData({ ...formData, position: e.target.value })} required />
              </div>
              <div className="form-group">
                <label className="form-label">Phone</label>
                <input className="form-input modal-input" value={formData.phone} onChange={(e) => setFormData({ ...formData, phone: e.target.value })} />
              </div>
              <div className="form-group">
                <label className="form-label">Status</label>
                <select className="form-input modal-input" value={formData.status} onChange={(e) => setFormData({ ...formData, status: e.target.value })}>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
              <div className="modal-actions">
                <button type="button" className="btn-ghost" onClick={() => setEditModal(null)}>Cancel</button>
                <button type="submit" className="btn-primary" disabled={formLoading}>
                  {formLoading ? <div className="btn-spinner" /> : 'Save Changes'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Delete Confirm Modal */}
      {deleteModal && (
        <div className="modal-overlay" onClick={() => setDeleteModal(null)}>
          <div className="modal-card animate-scale-in" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>Confirm Deactivation</h3>
              <button className="modal-close" onClick={() => setDeleteModal(null)}>×</button>
            </div>
            <div className="modal-body">
              {formError && <div className="auth-error">{formError}</div>}
              <p className="modal-text">
                Are you sure you want to deactivate <strong>{deleteModal.name}</strong>? 
                This will set their status to inactive.
              </p>
            </div>
            <div className="modal-actions">
              <button className="btn-ghost" onClick={() => setDeleteModal(null)}>Cancel</button>
              <button className="btn-danger" onClick={handleDelete} disabled={formLoading}>
                {formLoading ? <div className="btn-spinner" /> : 'Deactivate'}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Add Modal */}
      {addModal && (
        <div className="modal-overlay" onClick={() => setAddModal(false)}>
          <div className="modal-card animate-scale-in" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>Add Recruiter</h3>
              <button className="modal-close" onClick={() => setAddModal(false)}>×</button>
            </div>
            <form onSubmit={handleAddSubmit}>
              {formError && <div className="auth-error">{formError}</div>}
              <div className="form-group">
                <label className="form-label">Company Name</label>
                <input className="form-input modal-input" value={formData.company_name} onChange={(e) => setFormData({ ...formData, company_name: e.target.value })} required />
              </div>
              <div className="form-group">
                <label className="form-label">Position</label>
                <input className="form-input modal-input" value={formData.position} onChange={(e) => setFormData({ ...formData, position: e.target.value })} required />
              </div>
              <div className="form-group">
                <label className="form-label">Phone</label>
                <input className="form-input modal-input" value={formData.phone} onChange={(e) => setFormData({ ...formData, phone: e.target.value })} />
              </div>
              <div className="modal-actions">
                <button type="button" className="btn-ghost" onClick={() => setAddModal(false)}>Cancel</button>
                <button type="submit" className="btn-primary" disabled={formLoading}>
                  {formLoading ? <div className="btn-spinner" /> : 'Create Recruiter'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
