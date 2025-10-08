<?php
// External Vehicle Approvals UI (PHP version)
// Drop-in page for external departments (Laravel-friendly). Can be placed under public/.
// Uses client-side calls to your API at /api/external-approvals.php with a shared key.
// Prefill via query: ?api=...&key=...&status=pending|approved|rejected

$api = isset($_GET['api']) ? trim((string)$_GET['api']) : '';
$key = isset($_GET['key']) ? trim((string)$_GET['key']) : '';
$status = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : 'pending';
if (!in_array($status, ['pending','approved','rejected'], true)) { $status = 'pending'; }

// Default to your production domain API if not provided
$defaultApi = 'https://logistics2.jetlougetravels-ph.com/api/external-approvals.php';
$effectiveApi = $api !== '' ? $api : $defaultApi;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vehicle Request Approvals</title>
  <style>
    :root { --bg:#f7f9fc; --card:#fff; --text:#1f2937; --muted:#6b7280; --primary:#0f3d64; --success:#198754; --danger:#dc3545; --border:#e5e7eb; }
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:var(--bg); color:var(--text); }
    .container { max-width: 1080px; margin: 24px auto; padding: 0 16px; }
    .card { background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.06); }
    .card-header { padding: 16px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .card-title { font-weight:700; font-size: 18px; }
    .card-body { padding: 16px 18px; }
    .row { display:flex; flex-wrap:wrap; gap:12px; }
    .col { flex:1 1 240px; }
    label { display:block; font-size:12px; color:var(--muted); margin-bottom:4px; }
    input[type="text"], textarea, select { width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; background:#fff; color:inherit; box-sizing:border-box; }
    textarea { min-height:70px; resize:vertical; }
    .btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; border:1px solid transparent; cursor:pointer; font-weight:600; font-size:14px; }
    .btn:disabled { opacity:0.6; cursor:not-allowed; }
    .btn-primary { background:var(--primary); color:#fff; }
    .btn-light { background:#fff; color:var(--text); border-color:var(--border); }
    .btn-success { background:var(--success); color:#fff; }
    .btn-danger { background:var(--danger); color:#fff; }
    .toolbar { display:flex; flex-wrap:wrap; gap:8px; }
    .status-pill { display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; font-weight:700; }
    .status-pending { background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
    .status-approved { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
    .status-rejected { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
    table { width:100%; border-collapse:separate; border-spacing:0; }
    th, td { text-align:left; padding:10px 10px; border-bottom:1px solid var(--border); vertical-align:top; }
    th { font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
    tr:hover td { background:#fafafa; }
    .actions { display:flex; gap:6px; flex-wrap:wrap; }
    .note { font-size:12px; color:var(--muted); }
    .muted { color:var(--muted); }
    .hidden { display:none !important; }
    .footer { margin-top: 18px; font-size:12px; color:var(--muted); }
    @media (max-width:720px){
      .hide-sm { display:none; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Vehicle Request Approvals</div>
        <div class="toolbar">
          <button class="btn btn-light" id="loadPendingBtn">Pending</button>
          <button class="btn btn-light" id="loadApprovedBtn">Approved</button>
          <button class="btn btn-light" id="loadRejectedBtn">Rejected</button>
          <button class="btn btn-primary" id="refreshBtn">Refresh</button>
        </div>
      </div>
      <div class="card-body">
        <div class="row" style="margin-bottom:12px;">
          <div class="col">
            <label>API URL</label>
            <input type="text" id="apiUrl" placeholder="https://your-domain/api/external-approvals.php" />
          </div>
          <div class="col">
            <label>Approval Key</label>
            <input type="text" id="apiKey" placeholder="Enter approval key (provided by Logistics)" />
          </div>
          <div class="col" style="align-self:flex-end;">
            <button class="btn btn-primary" id="saveCfgBtn">Save</button>
          </div>
        </div>

        <div id="statusMsg" class="muted" style="margin-bottom:8px;"></div>

        <div class="table-wrap">
          <table id="requestsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Requester</th>
                <th>Vehicle Type</th>
                <th class="hide-sm">Justification</th>
                <th class="hide-sm">Request Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="requestsBody">
              <tr><td colspan="7" class="muted">No data.</td></tr>
            </tbody>
          </table>
        </div>

        <div class="footer">
          This page lets authorized approvers view and approve/reject vehicle requests. Your approval key is used only to authenticate API calls and is not shared.
        </div>
      </div>
    </div>
  </div>

<script>
// Server-provided defaults (can be overridden by local storage or query string)
window.EXT_DEFAULTS = {
  apiUrl: <?php echo json_encode($effectiveApi); ?>,
  apiKey: <?php echo json_encode($key); ?>,
  status: <?php echo json_encode($status); ?>
};
</script>
<script>
(function(){
  const els = {
    apiUrl: document.getElementById('apiUrl'),
    apiKey: document.getElementById('apiKey'),
    saveCfgBtn: document.getElementById('saveCfgBtn'),
    loadPendingBtn: document.getElementById('loadPendingBtn'),
    loadApprovedBtn: document.getElementById('loadApprovedBtn'),
    loadRejectedBtn: document.getElementById('loadRejectedBtn'),
    refreshBtn: document.getElementById('refreshBtn'),
    statusMsg: document.getElementById('statusMsg'),
    tbody: document.getElementById('requestsBody'),
  };

  const state = {
    status: (window.EXT_DEFAULTS && window.EXT_DEFAULTS.status) || 'pending',
    apiUrl: (window.EXT_DEFAULTS && window.EXT_DEFAULTS.apiUrl) || '',
    apiKey: (window.EXT_DEFAULTS && window.EXT_DEFAULTS.apiKey) || '',
    items: [],
  };

  function defaultApiUrl(){
    try {
      const base = window.location.origin || '';
      if (base && base !== 'null' && base !== 'file://') return base.replace(/\/$/, '') + '/api/external-approvals.php';
    } catch(_) {}
    return 'https://logistics2.jetlougetravels-ph.com/api/external-approvals.php';
  }

  function loadCfg(){
    try {
      const lsApi = localStorage.getItem('ext_api_url');
      const lsKey = localStorage.getItem('ext_api_key');
      state.apiUrl = (lsApi && lsApi.trim()) || state.apiUrl || defaultApiUrl();
      state.apiKey = (lsKey && lsKey.trim()) || state.apiKey || '';
      els.apiUrl.value = state.apiUrl;
      els.apiKey.value = state.apiKey;
    } catch(_) {
      state.apiUrl = state.apiUrl || defaultApiUrl();
      els.apiUrl.value = state.apiUrl;
    }
  }

  function saveCfg(){
    state.apiUrl = (els.apiUrl.value || '').trim();
    state.apiKey = (els.apiKey.value || '').trim();
    try { localStorage.setItem('ext_api_url', state.apiUrl); } catch(_) {}
    try { localStorage.setItem('ext_api_key', state.apiKey); } catch(_) {}
    info('Saved settings');
  }

  function info(msg){ els.statusMsg.textContent = msg || ''; }

  async function apiGet(action, params){
    const u = new URL(state.apiUrl);
    u.searchParams.set('action', action);
    Object.keys(params||{}).forEach(k => { if (params[k] != null) u.searchParams.set(k, params[k]); });
    const res = await fetch(u.toString(), {
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + state.apiKey,
      }
    });
    const data = await res.json().catch(()=>({success:false,message:'Invalid JSON'}));
    if (!res.ok || data.success === false) {
      throw new Error(data && data.message ? data.message : 'Request failed');
    }
    return data;
  }

  async function apiPost(action, body){
    const res = await fetch(state.apiUrl + '?action=' + encodeURIComponent(action), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + state.apiKey,
      },
      body: JSON.stringify(body || {})
    });
    const data = await res.json().catch(()=>({success:false,message:'Invalid JSON'}));
    if (!res.ok || data.success === false) {
      throw new Error(data && data.message ? data.message : 'Request failed');
    }
    return data;
  }

  function statusPill(s){
    s = String(s||'').toLowerCase();
    const cls = s === 'approved' ? 'status-approved' : s === 'rejected' ? 'status-rejected' : 'status-pending';
    const label = s ? s : 'unknown';
    return '<span class="status-pill ' + cls + '">' + label + '</span>';
  }

  function escapeHtml(x){
    return String(x==null?'':x).replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  }

  function renderRows(items){
    if (!Array.isArray(items) || items.length === 0){
      els.tbody.innerHTML = '<tr><td colspan="7" class="muted">No data.</td></tr>';
      return;
    }
    const rows = items.map(r => {
      const noteId = 'note_' + r.id;
      return (
        '<tr>' +
          '<td>#' + r.id + '</td>' +
          '<td>' + escapeHtml(r.requester_name || ('ID #' + (r.requester_id||''))) + '</td>' +
          '<td>' + escapeHtml(r.vehicle_type || '') + '</td>' +
          '<td class="hide-sm">' + escapeHtml(r.justification || '') + '</td>' +
          '<td class="hide-sm">' + escapeHtml(r.request_date || '') + '</td>' +
          '<td>' + statusPill(r.status) + '</td>' +
          '<td>' +
            '<div class="actions">' +
              '<textarea id="' + noteId + '" placeholder="Optional note" style="min-width:200px"></textarea>' +
              (r.status === 'pending'
                ? '<button class="btn btn-success" data-act="approve" data-id="' + r.id + '">Approve</button>' +
                  '<button class="btn btn-danger" data-act="reject" data-id="' + r.id + '">Reject</button>'
                : '<span class="note">No actions</span>') +
            '</div>' +
          '</td>' +
        '</tr>'
      );
    }).join('');
    els.tbody.innerHTML = rows;
  }

  async function loadList(status){
    if (status) state.status = status;
    info('Loading ' + state.status + ' requests...');
    try {
      const data = await apiGet('list', { status: state.status });
      const items = (data && data.data && data.data.requests) ? data.data.requests : [];
      state.items = items;
      renderRows(items);
      info('Loaded ' + items.length + ' ' + state.status + ' requests');
    } catch (e) {
      renderRows([]);
      info('Error: ' + (e && e.message ? e.message : 'Failed to load'));
    }
  }

  async function onActionClick(e){
    const t = e.target;
    if (!(t && t.dataset && t.dataset.act)) return;
    const act = t.dataset.act;
    const id = parseInt(t.dataset.id, 10);
    const noteEl = document.getElementById('note_' + id);
    const note = noteEl ? noteEl.value : '';
    if (!id || !['approve','reject'].includes(act)) return;

    if (!confirm('Confirm to ' + act + ' request #' + id + '?')) return;

    t.disabled = true;
    try {
      const body = { request_id: id, decision: act, note: note || '' };
      const res = await apiPost('decide', body);
      info('Updated request #' + id + ' -> ' + res.data.status);
      // Refresh current list
      await loadList(state.status);
    } catch (e) {
      info('Error: ' + (e && e.message ? e.message : 'Failed to update'));
    } finally {
      t.disabled = false;
    }
  }

  function bind(){
    els.saveCfgBtn.addEventListener('click', saveCfg);
    els.loadPendingBtn.addEventListener('click', () => loadList('pending'));
    els.loadApprovedBtn.addEventListener('click', () => loadList('approved'));
    els.loadRejectedBtn.addEventListener('click', () => loadList('rejected'));
    els.refreshBtn.addEventListener('click', () => loadList(state.status));
    els.tbody.addEventListener('click', onActionClick);
  }

  function prefillFromQuery(){
    try {
      const sp = new URLSearchParams(window.location.search);
      const qApi = (sp.get('api') || '').trim();
      const qKey = (sp.get('key') || '').trim();
      const qStatus = (sp.get('status') || '').trim();
      let changed = false;
      if (qApi) { els.apiUrl.value = qApi; state.apiUrl = qApi; changed = true; }
      if (qKey) { els.apiKey.value = qKey; state.apiKey = qKey; changed = true; }
      if (qStatus && ['pending','approved','rejected'].includes(qStatus.toLowerCase())) {
        state.status = qStatus.toLowerCase();
      }
      if (changed) { saveCfg(); }
    } catch(_) {}
  }

  function init(){
    loadCfg();
    prefillFromQuery();
    bind();
    loadList(state.status || 'pending');
  }

  init();
})();
</script>
</body>
</html>
