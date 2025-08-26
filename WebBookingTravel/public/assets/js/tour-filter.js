/*
 * Tour Filter UI & Data Fetch
 * Features:
 *  - Lazy fetch destinations (unique departurePoint + tour count)
 *  - Lazy fetch pickup points (pickupPoint list)
 *  - Custom overlay selector for Destinations with search + counts
 *  - Custom dropdown for Pickup (inline panel)
 *  - Graceful fallback sample data if API fails
 *  - Accessible keyboard navigation
 *
 * Expected API endpoints (adjust if different):
 *   GET /api/tours/destinations -> [{ departurePoint: string, count: number }]
 *   GET /api/tours/pickup-points -> ["Hà Nội", "Đà Nẵng", ...]
 */
(function() {
  const API_DESTINATIONS = '/api/v1/tours/departure-points';
  const API_PICKUPS = '/api/v1/tours/pickup-points';
  const selDestination = document.getElementById('city'); // original select (destination)
  const selPickup = document.getElementById('activity'); // original select (pickup / departure)
  if(!selDestination || !selPickup) return;

  // Hide original selects but keep for form submission
  selDestination.style.display = 'none';
  selPickup.style.display = 'none';

  // State caches
  let destinationsCache = null; // [{departurePoint, count}]
  let pickupsCache = null; // [string]

  // Create visible replacement controls
  const destinationWrapper = createPickerWrapper('Chọn điểm đến', 'Bạn muốn đi đâu?');
  selDestination.parentNode.appendChild(destinationWrapper.container);

  const pickupWrapper = createPickerWrapper('Chọn điểm đón', 'Khởi hành từ đâu?');
  selPickup.parentNode.appendChild(pickupWrapper.container);

  // Overlay for destinations (full screen modal style)
  const overlay = document.createElement('div');
  overlay.className = 'tour-filter-overlay hidden';
  overlay.innerHTML = `
    <div class="tfo-dialog" role="dialog" aria-modal="true" aria-labelledby="tfoTitle">
      <div class="tfo-head">
        <h3 id="tfoTitle">Chọn điểm đến</h3>
        <button class="tfo-close" aria-label="Đóng">&times;</button>
      </div>
      <div class="tfo-search-bar">
        <i class="far fa-search"></i>
        <input type="text" placeholder="Tìm điểm đến..." class="tfo-search" />
      </div>
      <div class="tfo-body">
        <div class="tfo-list" role="listbox" tabindex="0" aria-label="Danh sách điểm đến"></div>
      </div>
      <div class="tfo-foot small text-muted">
        <span class="tfo-count"></span>
        <button class="tfo-clear" type="button">Xóa chọn</button>
        <button class="tfo-apply theme-btn" type="button"><span data-hover="Chọn">Chọn</span><i class="fal fa-check"></i></button>
      </div>
    </div>`;
  document.body.appendChild(overlay);

  // Inline pickup dropdown panel
  const pickupPanel = document.createElement('div');
  pickupPanel.className = 'pickup-panel hidden';
  pickupPanel.innerHTML = `
    <div class="pickup-inner">
      <div class="pickup-head d-flex justify-content-between align-items-center">
        <strong>Điểm đón</strong>
        <button class="pickup-close" aria-label="Đóng">&times;</button>
      </div>
      <div class="pickup-list" role="listbox" tabindex="0"></div>
    </div>`;
  pickupWrapper.container.appendChild(pickupPanel);

  // Elements references
  const destDisplay = destinationWrapper.display;
  const pickupDisplay = pickupWrapper.display;
  const overlayDialog = overlay.querySelector('.tfo-dialog');
  const overlaySearch = overlay.querySelector('.tfo-search');
  const overlayList = overlay.querySelector('.tfo-list');
  const overlayCount = overlay.querySelector('.tfo-count');
  const overlayCloseBtn = overlay.querySelector('.tfo-close');
  const overlayApplyBtn = overlay.querySelector('.tfo-apply');
  const overlayClearBtn = overlay.querySelector('.tfo-clear');
  const pickupListEl = pickupPanel.querySelector('.pickup-list');
  const pickupCloseBtn = pickupPanel.querySelector('.pickup-close');

  let selectedDestination = null; // {departurePoint, count}
  let filteredDestinations = [];
  let highlightedIndex = -1;

  // Style injection (scoped minimal)
  injectStyles();

  // Events - open overlays
  destDisplay.addEventListener('click', openDestinationOverlay);
  destinationWrapper.button.addEventListener('click', openDestinationOverlay);

  pickupDisplay.addEventListener('click', openPickupPanel);
  pickupWrapper.button.addEventListener('click', openPickupPanel);

  // Close buttons
  overlayCloseBtn.addEventListener('click', () => closeOverlay());
  pickupCloseBtn.addEventListener('click', () => hidePickupPanel());

  // Overlay outside click
  overlay.addEventListener('click', e => {
    if(e.target === overlay) closeOverlay();
  });

  // Keyboard close
  document.addEventListener('keydown', e => {
    if(e.key === 'Escape') { if(!overlay.classList.contains('hidden')) closeOverlay(); hidePickupPanel(); }
  });

  // Search filtering
  overlaySearch.addEventListener('input', () => renderDestinationList(overlaySearch.value.trim().toLowerCase()));

  // Apply & Clear
  overlayApplyBtn.addEventListener('click', applyDestinationSelection);
  overlayClearBtn.addEventListener('click', () => {
    selectedDestination = null;
    destDisplay.textContent = 'Bạn muốn đi đâu?';
    selDestination.value = '';
    renderDestinationList(overlaySearch.value.trim().toLowerCase());
  });

  // Pickup panel outside click (using capture to avoid immediate close)
  document.addEventListener('click', e => {
    if(!pickupPanel.contains(e.target) && !pickupWrapper.container.contains(e.target)) {
      hidePickupPanel();
    }
  });

  // Functions
  function createPickerWrapper(label, placeholder) {
    const container = document.createElement('div');
    container.className = 'tf-picker-wrapper';
    container.innerHTML = `
      <div class="tf-label small text-muted">${label}</div>
      <div class="tf-display" tabindex="0" role="button" aria-label="${label}">${placeholder}</div>
      <button type="button" class="tf-caret" aria-hidden="true"><i class="far fa-chevron-down"></i></button>`;
    return { container, display: container.querySelector('.tf-display'), button: container.querySelector('.tf-caret') };
  }

  async function openDestinationOverlay() {
    if(destinationsCache === null) {
  setLoading(overlayList, 'Đang tải điểm đến...');
  destinationsCache = await fetchDestinations();
    }
    overlay.classList.remove('hidden');
    document.documentElement.classList.add('no-scroll');
    overlaySearch.value = '';
    renderDestinationList('');
    setTimeout(() => overlaySearch.focus(), 30);
  }

  function closeOverlay() {
    overlay.classList.add('hidden');
    document.documentElement.classList.remove('no-scroll');
  }

  async function openPickupPanel() {
    if(pickupsCache === null) {
  setLoading(pickupListEl, 'Đang tải điểm đón...');
  pickupsCache = await fetchPickups();
    }
    renderPickupList();
    pickupPanel.classList.remove('hidden');
  }

  function hidePickupPanel() {
    pickupPanel.classList.add('hidden');
  }

  function applyDestinationSelection() {
    if(selectedDestination) {
      destDisplay.textContent = selectedDestination.departurePoint + ' ('+ selectedDestination.count +')';
      selDestination.value = selectedDestination.departurePoint;
    }
    closeOverlay();
  }

  async function fetchDestinations() {
    try {
      const res = await fetch(API_DESTINATIONS, { headers: { 'Accept': 'application/json' }});
      if(!res.ok) throw new Error('Bad status');
      const data = await res.json();
      if(Array.isArray(data)) {
        const cleaned = data.filter(d => d && d.departurePoint);
        if(cleaned.length === 0) console.warn('Destinations API returned empty list');
        return cleaned;
      }
  return [];
    } catch(err) {
  console.error('Destination fetch error', err);
  return [];
    }
  }

  async function fetchPickups() {
    try {
      const res = await fetch(API_PICKUPS, { headers: { 'Accept': 'application/json' }});
      if(!res.ok) throw new Error('Bad status');
      const data = await res.json();
      if(Array.isArray(data)) {
        const cleaned = data.filter(p => !!p);
        if(cleaned.length === 0) console.warn('Pickup API empty list');
        return cleaned;
      }
  return [];
    } catch(err) {
  console.error('Pickup fetch error', err);
  return [];
    }
  }

  function renderDestinationList(filter) {
    if(!destinationsCache) return;
    filteredDestinations = destinationsCache.filter(item => !filter || item.departurePoint.toLowerCase().includes(filter));
    overlayList.innerHTML = '';
    highlightedIndex = -1;
    if(filteredDestinations.length === 0) {
      setEmpty(overlayList, 'Không có điểm đến phù hợp');
      overlayCount.textContent = '0 điểm đến';
      return;
    }
    filteredDestinations.forEach((item, idx) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'tfo-item' + (selectedDestination && selectedDestination.departurePoint === item.departurePoint ? ' selected' : '');
      btn.setAttribute('role', 'option');
      btn.dataset.index = idx;
      btn.innerHTML = `<span class="tfo-name">${item.departurePoint}</span><span class="tfo-badge">${item.count}</span>`;
      btn.addEventListener('click', () => {
        selectedDestination = item;
        applyDestinationSelection();
      });
      overlayList.appendChild(btn);
    });
    overlayCount.textContent = filteredDestinations.length + ' điểm đến';
  }

  function renderPickupList() {
    pickupListEl.innerHTML = '';
    if(!pickupsCache || pickupsCache.length === 0) {
      setEmpty(pickupListEl, 'Không có điểm đón');
      return;
    }
    pickupsCache.forEach(name => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'pickup-item';
      btn.textContent = name;
      btn.addEventListener('click', () => {
        pickupDisplay.textContent = name;
        selPickup.value = name;
        hidePickupPanel();
      });
      pickupListEl.appendChild(btn);
    });
  }

  // Keyboard navigation within overlay list
  overlayList.addEventListener('keydown', e => {
    if(['ArrowDown','ArrowUp','Enter'].includes(e.key)) e.preventDefault();
    if(e.key === 'ArrowDown') moveHighlight(1);
    if(e.key === 'ArrowUp') moveHighlight(-1);
    if(e.key === 'Enter' && highlightedIndex >=0) {
      selectedDestination = filteredDestinations[highlightedIndex];
      applyDestinationSelection();
    }
  });

  function moveHighlight(delta) {
    if(filteredDestinations.length === 0) return;
    highlightedIndex = (highlightedIndex + delta + filteredDestinations.length) % filteredDestinations.length;
    [...overlayList.children].forEach((el,i) => {
      if(i === highlightedIndex) el.classList.add('highlight'); else el.classList.remove('highlight');
    });
    const activeEl = overlayList.children[highlightedIndex];
    if(activeEl) {
      const rect = activeEl.getBoundingClientRect();
      const containerRect = overlayList.getBoundingClientRect();
      if(rect.bottom > containerRect.bottom) activeEl.scrollIntoView(false);
      if(rect.top < containerRect.top) activeEl.scrollIntoView();
    }
  }

  function injectStyles() {
    if(document.getElementById('tourFilterStyles')) return;
    const css = `
      .no-scroll { overflow: hidden; }
      .tf-picker-wrapper { position: relative; background:#fff; border:1px solid #e4e6eb; padding:10px 14px; border-radius:14px; display:flex; align-items:center; gap:10px; cursor:pointer; min-width:220px; }
      .tf-picker-wrapper:hover { border-color:#ff9d3c; box-shadow:0 0 0 3px rgba(255,157,60,.15); }
      .tf-picker-wrapper .tf-label { position:absolute; top:4px; left:16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; opacity:.65; }
      .tf-picker-wrapper .tf-display { flex:1; padding-top:12px; font-weight:500; }
      .tf-picker-wrapper .tf-display:focus { outline:none; }
      .tf-picker-wrapper .tf-caret { background:transparent; border:0; color:#555; cursor:pointer; }
      .tf-picker-wrapper .tf-caret i { transition: transform .3s; }
      .pickup-panel { position:absolute; top:100%; left:0; margin-top:8px; background:#fff; box-shadow:0 12px 32px -8px rgba(0,0,0,.18); border-radius:18px; padding:14px 16px 16px; width:100%; z-index:40; }
      .pickup-panel.hidden { display:none; }
      .pickup-panel .pickup-list { max-height:240px; overflow:auto; display:grid; gap:6px; }
      .pickup-panel .pickup-item { text-align:left; background:#f6f7f9; border:0; padding:8px 12px; border-radius:10px; font-size:14px; cursor:pointer; transition: background .25s; }
      .pickup-panel .pickup-item:hover { background:#ffefe0; }
      .pickup-panel .pickup-head { margin-bottom:8px; }
      .pickup-panel .pickup-close { background:transparent; border:0; font-size:22px; line-height:1; cursor:pointer; }
      .tour-filter-overlay { position:fixed; inset:0; background:rgba(15,15,20,.55); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); display:flex; align-items:center; justify-content:center; z-index:1000; padding:24px; }
      .tour-filter-overlay.hidden { display:none; }
      .tfo-dialog { width:min(760px,100%); background:#fffffffa; border-radius:28px; padding:20px 26px 18px; display:flex; flex-direction:column; max-height:100%; box-shadow:0 20px 50px -15px rgba(0,0,0,.35); position:relative; }
      .tfo-head { display:flex; align-items:center; justify-content:space-between; gap:12px; }
      .tfo-head h3 { margin:0; font-size:20px; font-weight:600; background:linear-gradient(90deg,#ff8a1f,#ffb347); -webkit-background-clip:text; color:transparent; }
      .tfo-close { background:#ffe7d0; border:0; width:38px; height:38px; display:flex; align-items:center; justify-content:center; font-size:26px; border-radius:14px; cursor:pointer; color:#ff7a00; }
      .tfo-close:hover { background:#ffd9b5; }
      .tfo-search-bar { margin:14px 0 10px; background:#f4f5f7; display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:16px; border:1px solid #eceff2; }
      .tfo-search-bar input { flex:1; background:transparent; border:0; outline:none; font-size:15px; }
      .tfo-body { overflow:auto; flex:1; padding-right:4px; }
      .tfo-list { display:grid; gap:8px; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); }
      .tfo-item { position:relative; text-align:left; background:#f6f7f9; border:0; padding:14px 16px 12px; border-radius:18px; font-size:14px; cursor:pointer; font-weight:500; display:flex; align-items:center; justify-content:space-between; transition: background .25s, transform .25s, box-shadow .25s; }
      .tfo-item:hover { background:#ffefe3; }
      .tfo-item.selected { background:#ffcf9f; box-shadow:0 4px 14px -4px rgba(255,132,0,.4); }
      .tfo-item.highlight { outline:2px solid #ff9d3c; }
      .tfo-item .tfo-badge { background:#fff; color:#ff7a00; padding:2px 9px 3px; border-radius:20px; font-size:12px; font-weight:600; box-shadow:0 2px 4px -2px rgba(0,0,0,.25); }
      .tfo-foot { display:flex; align-items:center; justify-content:flex-end; gap:12px; padding-top:12px; margin-top:12px; border-top:1px solid #eee; }
      .tfo-foot .tfo-count { margin-right:auto; font-size:13px; }
      .tfo-foot button { cursor:pointer; }
      .tfo-clear { background:transparent; border:0; color:#ff7a00; font-size:14px; font-weight:500; }
      .tfo-clear:hover { text-decoration:underline; }
      .tfo-apply { display:inline-flex; align-items:center; gap:8px; border:0; background: linear-gradient(90deg,#ff931f,#ffb347); color:#fff; padding:10px 18px; font-size:14px; border-radius:14px; font-weight:600; }
      .tfo-apply:hover { filter:brightness(1.05); }
      @media (max-width:640px) { .tfo-dialog { padding:18px 18px 14px; } .tfo-list { grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); } }
    .tf-state { padding:24px 10px; text-align:center; color:#666; font-size:14px; grid-column:1/-1; }
    `;
    const style = document.createElement('style');
    style.id = 'tourFilterStyles';
    style.textContent = css;
    document.head.appendChild(style);
  }

  function setLoading(container, msg) { container.innerHTML = `<div class="tf-state">${msg}</div>`; }
  function setEmpty(container, msg) { container.innerHTML = `<div class="tf-state">${msg}</div>`; }
})();
