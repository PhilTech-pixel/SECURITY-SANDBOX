<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Security Sandbox</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
  --bg: #0f172a;
  --card: #111827;
  --soft: #1f2937;
  --accent: #3b82f6;
  --text: #e5e7eb;
  --muted: #9ca3af;
  --success: #22c55e;
  --danger: #ef4444;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Inter', sans-serif;
  background: radial-gradient(circle at top, #0f172a, #020617);
  color: var(--text);
}

.container {
  max-width: 1200px;
  margin: auto;
  padding: 40px 20px;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
}

h1 {
  font-size: 1.6rem;
  font-weight: 600;
}

.nav a {
  margin-left: 20px;
  text-decoration: none;
  color: var(--muted);
  font-size: 0.9rem;
}

.nav a:hover {
  color: white;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 24px;
}

.card {
  background: var(--card);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid rgba(255,255,255,0.05);
  transition: 0.2s ease;
}

.card:hover {
  transform: translateY(-4px);
  border-color: rgba(255,255,255,0.1);
}

.card h2 {
  margin: 0 0 5px;
  font-size: 1.1rem;
}

.card p {
  margin: 0 0 15px;
  color: var(--muted);
  font-size: 0.85rem;
}

label {
  font-size: 0.75rem;
  color: var(--muted);
  display: block;
  margin-bottom: 6px;
}

input {
  width: 100%;
  padding: 10px;
  background: var(--soft);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 8px;
  color: white;
  margin-bottom: 12px;
  font-family: monospace;
}

input:focus {
  outline: none;
  border-color: var(--accent);
}

.output {
  background: #020617;
  border-radius: 8px;
  padding: 10px;
  font-size: 0.8rem;
  margin-bottom: 10px;
  border: 1px solid rgba(255,255,255,0.05);
}

button {
  background: var(--accent);
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  font-size: 0.8rem;
}

button.secondary {
  background: var(--soft);
}

button.danger {
  background: var(--danger);
}

.badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  margin-top: 6px;
}

.success { background: rgba(34,197,94,0.15); color: var(--success); }
.error { background: rgba(239,68,68,0.15); color: var(--danger); }

.note {
  font-size: 0.7rem;
  color: var(--muted);
  margin-top: 10px;
}

</style>
</head>
<body>

<div class="container">

<header>
  <h1>Security Sandbox</h1>
  <div class="nav">
    <a href="#">Live</a>
    <a href="#">Report</a>
  </div>
</header>

<div class="grid">

<!-- XSS -->
<div class="card">
  <h2>HTML Encoding</h2>
  <p>XSS protection demo</p>

  <label>Input</label>
  <input id="htmlInput" value="<script>alert(1)</script>">

  <div class="output" id="encoded"></div>
  <div class="output" id="raw"></div>

  <div class="note">Encoded output is safe. Raw output executes.</div>
</div>

<!-- SQL -->
<div class="card">
  <h2>SQL Injection</h2>
  <p>Compare query handling</p>

  <label>Query</label>
  <input id="sqlInput" value="' OR '1'='1">

  <div style="display:flex; gap:10px; margin-bottom:10px;">
    <button onclick="run('vulnerable')" class="danger">Vulnerable</button>
    <button onclick="run('safe')">Safe</button>
  </div>

  <div class="output" id="query"></div>
  <div class="output" id="results"></div>
</div>

<!-- EMAIL -->
<div class="card">
  <h2>Email Validation</h2>
  <p>Server-side validation</p>

  <label>Email</label>
  <input id="emailInput" value="test@gmail.com">

  <button onclick="validateEmail()">Submit</button>

  <div id="emailResult"></div>
</div>

</div>

</div>

<script>

async function updateHTML() {
  const val = document.getElementById('htmlInput').value;
  const res = await fetch('api.php?action=html_encode', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({input: val})
  });
  const data = await res.json();

  document.getElementById('encoded').textContent = data.encoded;
  document.getElementById('raw').innerHTML = data.raw;
}

document.getElementById('htmlInput').addEventListener('input', updateHTML);
updateHTML();

async function run(type) {
  const val = document.getElementById('sqlInput').value;
  const res = await fetch('api.php?action=sql_search', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({term: val, type})
  });
  const data = await res.json();

  document.getElementById('query').textContent = data.query || '';
  document.getElementById('results').textContent = JSON.stringify(data.results || data.message);
}

async function validateEmail() {
  const val = document.getElementById('emailInput').value;
  const res = await fetch('api.php?action=subscribe', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({email: val})
  });
  const data = await res.json();

  const el = document.getElementById('emailResult');
  el.className = 'badge ' + (data.status === 'success' ? 'success' : 'error');
  el.textContent = data.message;
}

</script>

</body>
</html>