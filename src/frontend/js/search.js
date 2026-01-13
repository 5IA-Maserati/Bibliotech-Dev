const books = [
  /* fare collegamento PHP con database libri */
];

const qEl = document.getElementById('q');
const genreEl = document.getElementById('genre');
const sortEl = document.getElementById('sort');
const resultsEl = document.getElementById('results');
const countEl = document.getElementById('count');

function renderList(list){
  resultsEl.innerHTML = '';
  countEl.textContent = list.length;
  if(list.length === 0){ resultsEl.innerHTML = '<p style="grid-column:1/-1;color:var(--muted)">Nessun risultato.</p>'; return }
  for(const b of list){
    const div = document.createElement('article'); div.className='book';
    div.innerHTML = `
      <div style="display:flex;gap:8px;align-items:flex-start">
        <div style="width:62px;height:88px;background:linear-gradient(180deg,#f3f7f7,#e9f3f2);border-radius:6px;flex:0 0 62px;display:flex;align-items:center;justify-content:center;color:#7aa;">📚</div>
        <div style="flex:1">
          <div class="title">${escapeHtml(b.title)}</div>
          <div class="meta">${escapeHtml(b.author)} — ${b.year} • ${escapeHtml(b.isbn)}</div>
          <div class="tags" style="margin-top:8px">
            <div class="tag">${escapeHtml(b.genre)}</div>
          </div>
        </div>
        <div class="status ${b.available? 'available':'unavailable'}">${b.available? 'Disponibile':'Non disponibile'}</div>
      </div>
    `;
    resultsEl.appendChild(div);
  }
}

function escapeHtml(s){ return String(s).replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])) }

function applyFilters(){
  const q = qEl.value.trim().toLowerCase();
  const genre = genreEl.value;
  let out = books.filter(b => {
    const matchQ = q === '' || [b.title,b.author,b.isbn].some(x => x.toLowerCase().includes(q));
    const matchGenre = !genre || b.genre === genre;
    return matchQ && matchGenre;
  });
  const sort = sortEl.value;
  if(sort === 'year-desc') out.sort((a,b)=>b.year-a.year);
  else if(sort === 'year-asc') out.sort((a,b)=>a.year-b.year);
  else out.sort((a,b)=>a.title.localeCompare(b.title));
  renderList(out);
}

qEl.addEventListener('input', debounce(applyFilters,200));
genreEl.addEventListener('change', applyFilters);
sortEl.addEventListener('change', applyFilters);

// helper
function debounce(fn,wait){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args),wait)} }

// inizializza
applyFilters();
