<?php
/**
 * The Face Tomb — main dashboard
 * Styled directory listing + persistent punch list.
 *
 * The punch list writes to punchlist.json in this same directory.
 * Make sure the deployed folder is writable by PHP if live editing fails.
 */

$currentDir = __DIR__;
$thisScript = basename(__FILE__);
$dataFile = $currentDir . '/punchlist.json';

function tomb_default_punchlist() {
    return [
        'items' => [
            [
                'id' => 'seed-doors',
                'text' => 'Create working doors between the outer chamber and future rooms.',
                'room' => 'Outer Chamber',
                'done' => false,
                'created' => date('c'),
                'updated' => date('c')
            ],
            [
                'id' => 'seed-room-transitions',
                'text' => 'Add room transition logic without breaking movement controls.',
                'room' => 'Traversal',
                'done' => false,
                'created' => date('c'),
                'updated' => date('c')
            ],
            [
                'id' => 'seed-image-folder',
                'text' => 'Use /images/ as the staging crypt for dashboard art, icons, and room concept images.',
                'room' => 'Dashboard',
                'done' => true,
                'created' => date('c'),
                'updated' => date('c')
            ]
        ]
    ];
}

function tomb_read_punchlist($dataFile) {
    if (!file_exists($dataFile)) {
        $default = tomb_default_punchlist();
        @file_put_contents($dataFile, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
        return $default;
    }

    $raw = file_get_contents($dataFile);
    $decoded = json_decode($raw, true);
    if (!is_array($decoded) || !isset($decoded['items']) || !is_array($decoded['items'])) {
        return tomb_default_punchlist();
    }
    return $decoded;
}

function tomb_write_punchlist($dataFile, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents($dataFile, $json, LOCK_EX) !== false;
}

function tomb_json_response($payload, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $action = $input['action'] ?? '';
    $data = tomb_read_punchlist($dataFile);
    $now = date('c');

    if ($action === 'add') {
        $text = trim((string)($input['text'] ?? ''));
        $room = trim((string)($input['room'] ?? 'General'));
        if ($text === '') {
            tomb_json_response(['ok' => false, 'error' => 'Punch list item text is required.'], 400);
        }
        $data['items'][] = [
            'id' => bin2hex(random_bytes(8)),
            'text' => mb_substr($text, 0, 500),
            'room' => mb_substr($room !== '' ? $room : 'General', 0, 80),
            'done' => false,
            'created' => $now,
            'updated' => $now
        ];
    } elseif ($action === 'toggle') {
        $id = (string)($input['id'] ?? '');
        foreach ($data['items'] as &$item) {
            if (($item['id'] ?? '') === $id) {
                $item['done'] = empty($item['done']);
                $item['updated'] = $now;
                break;
            }
        }
        unset($item);
    } elseif ($action === 'delete') {
        $id = (string)($input['id'] ?? '');
        $data['items'] = array_values(array_filter($data['items'], function($item) use ($id) {
            return ($item['id'] ?? '') !== $id;
        }));
    } elseif ($action === 'edit') {
        $id = (string)($input['id'] ?? '');
        $text = trim((string)($input['text'] ?? ''));
        $room = trim((string)($input['room'] ?? 'General'));
        if ($text === '') {
            tomb_json_response(['ok' => false, 'error' => 'Edited item text is required.'], 400);
        }
        foreach ($data['items'] as &$item) {
            if (($item['id'] ?? '') === $id) {
                $item['text'] = mb_substr($text, 0, 500);
                $item['room'] = mb_substr($room !== '' ? $room : 'General', 0, 80);
                $item['updated'] = $now;
                break;
            }
        }
        unset($item);
    } else {
        tomb_json_response(['ok' => false, 'error' => 'Unknown action.'], 400);
    }

    if (!tomb_write_punchlist($dataFile, $data)) {
        tomb_json_response(['ok' => false, 'error' => 'Could not write punchlist.json. Check server file permissions.'], 500);
    }

    tomb_json_response(['ok' => true, 'items' => $data['items']]);
}

$punchData = tomb_read_punchlist($dataFile);
$items = array_values(array_diff(scandir($currentDir), ['..', '.']));
$hidden = [$thisScript, 'punchlist.json', '.git', '.github', '.gitignore', 'README.md'];
$directoryItems = [];

foreach ($items as $item) {
    if (in_array($item, $hidden, true)) continue;
    if (strpos($item, '.') === 0) continue;

    $path = $currentDir . '/' . $item;
    $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
    $isDir = is_dir($path);
    $href = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
    $label = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
    $thumb = '';

    if ($isDir) {
        $hasIndex = file_exists($path . '/index.html') || file_exists($path . '/index.php');
        $kind = $hasIndex ? 'Enterable Room' : 'Folder';
        $thumb = '<div class="glyph">⌂</div>';
    } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true)) {
        $thumb = '<img src="' . $href . '" alt="">';
        $kind = 'Image Relic';
    } elseif ($extension === 'hdl') {
        $potentialThumb = pathinfo($item, PATHINFO_FILENAME) . '.jpg';
        if (file_exists($currentDir . '/' . $potentialThumb)) {
            $thumb = '<img src="' . htmlspecialchars($potentialThumb, ENT_QUOTES, 'UTF-8') . '" alt="">';
        } else {
            $thumb = '<div class="glyph">&lt;HDL /&gt;</div>';
        }
        $kind = 'HDL Artifact';
    } elseif (in_array($extension, ['php', 'html', 'js', 'css'], true)) {
        $thumb = '<div class="glyph">⌁</div>';
        $kind = 'Interface File';
    } else {
        $thumb = '<div class="glyph">◇</div>';
        $kind = strtoupper($extension ?: 'File');
    }

    $directoryItems[] = [
        'label' => $label,
        'href' => $href,
        'kind' => $kind,
        'thumb' => $thumb,
        'mtime' => date('M j, Y H:i', filemtime($path) ?: time())
    ];
}

usort($directoryItems, function($a, $b) {
    if ($a['kind'] === $b['kind']) return strcasecmp($a['label'], $b['label']);
    return strcasecmp($a['kind'], $b['kind']);
});

$punchJson = json_encode($punchData['items'], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>The Face Tomb — Directory & Punch List</title>
  <style>
    *{box-sizing:border-box}
    :root{--bg:#050505;--panel:rgba(0,0,0,.72);--panel2:rgba(10,8,5,.82);--gold:#e7d29a;--gold2:#b78f32;--line:rgba(218,184,92,.32);--muted:rgba(255,255,255,.68);--danger:#b75a48;--done:#91c788}
    html,body{margin:0;min-height:100%;background:#050505;color:#fff;font-family:Arial,Helvetica,sans-serif}
    body{background:radial-gradient(circle at 50% 0%,rgba(92,70,31,.34),transparent 36rem),radial-gradient(circle at 20% 18%,rgba(183,143,50,.14),transparent 24rem),linear-gradient(180deg,#070605,#020202 62%,#000);padding:16px}
    a{color:inherit;text-decoration:none}.shell{max-width:1320px;margin:0 auto;display:grid;gap:14px}.hero,.panel{background:var(--panel);border:1px solid var(--line);box-shadow:0 0 42px rgba(0,0,0,.45);backdrop-filter:blur(4px)}
    .hero{padding:20px;display:grid;grid-template-columns:1.4fr .6fr;gap:18px;align-items:end}h1{margin:0 0 8px;font-size:clamp(26px,5vw,54px);letter-spacing:.18em;text-transform:uppercase;color:var(--gold);line-height:.95}.subtitle{max-width:760px;color:rgba(255,255,255,.82);line-height:1.45;margin:0}.seal{justify-self:end;width:min(150px,34vw);aspect-ratio:1;border-radius:50%;border:1px solid rgba(231,210,154,.5);display:grid;place-items:center;color:var(--gold);font-weight:bold;letter-spacing:.12em;background:radial-gradient(circle,rgba(231,210,154,.12),rgba(0,0,0,.7));box-shadow:inset 0 0 32px rgba(231,210,154,.08),0 0 36px rgba(0,0,0,.5)}
    .nav{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}.button,button{border:1px solid rgba(231,210,154,.38);background:rgba(218,184,92,.12);color:#f1dfae;padding:9px 11px;text-transform:uppercase;letter-spacing:.12em;font-size:11px;cursor:pointer}.button:hover,button:hover{background:rgba(218,184,92,.22)}
    .panel{padding:14px}.panel h2{margin:0 0 10px;color:var(--gold);font-size:14px;letter-spacing:.16em;text-transform:uppercase}.punchPanel{display:grid;gap:12px}.punchTop{display:grid;grid-template-columns:minmax(260px,1fr) minmax(260px,.78fr);gap:12px;align-items:start}.punchForm{display:grid;gap:8px}.row{display:grid;grid-template-columns:1fr auto;gap:8px}.items{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:8px}
    input,textarea{width:100%;background:rgba(0,0,0,.52);border:1px solid rgba(218,184,92,.26);color:#fff;padding:9px;font:inherit;border-radius:0;outline:none}textarea{min-height:66px;resize:vertical}input:focus,textarea:focus{border-color:rgba(231,210,154,.7)}.punch{border:1px solid rgba(218,184,92,.2);background:rgba(0,0,0,.36);padding:9px;display:grid;grid-template-columns:auto 1fr auto;gap:9px;align-items:start;min-height:76px}.punch.done{border-color:rgba(145,199,136,.35);opacity:.72}.punch.done .punchText{text-decoration:line-through;color:rgba(255,255,255,.62)}.check{width:20px;height:20px;accent-color:var(--gold2);margin-top:2px}.room{display:inline-block;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:var(--gold);margin-bottom:4px}.punchText{line-height:1.3;word-break:break-word}.meta{font-size:10px;color:var(--muted);margin-top:5px}.mini{font-size:10px;padding:6px 7px;letter-spacing:.08em}.delete{border-color:rgba(183,90,72,.42);color:#ffc7bd;background:rgba(183,90,72,.10)}.empty{color:var(--muted);border:1px dashed rgba(218,184,92,.25);padding:12px;text-align:center}.status{min-height:16px;color:var(--gold);font-size:12px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(118px,1fr));gap:8px}.card{background:var(--panel2);border:1px solid rgba(218,184,92,.22);min-height:132px;display:flex;flex-direction:column;transition:transform .16s ease,border-color .16s ease,background .16s ease}.card:hover{transform:translateY(-2px);border-color:rgba(231,210,154,.55);background:rgba(24,18,12,.9)}.thumb{height:68px;background:#16120d;border-bottom:1px solid rgba(218,184,92,.18);display:grid;place-items:center;overflow:hidden}.thumb img{width:100%;height:100%;object-fit:cover;display:block}.glyph{color:var(--gold);font-size:22px;opacity:.88;text-align:center;word-break:break-word;padding:8px}.cardBody{padding:8px;display:grid;gap:4px;flex:1}.kind{color:var(--gold);font-size:9px;letter-spacing:.12em;text-transform:uppercase;opacity:.85}.name{font-weight:bold;word-break:break-word;line-height:1.12;font-size:13px}.mtime{font-size:10px;color:var(--muted);margin-top:auto}
    @media(max-width:860px){.hero,.punchTop{grid-template-columns:1fr}.seal{justify-self:start}.row{grid-template-columns:1fr}.items{grid-template-columns:1fr}.grid{grid-template-columns:repeat(auto-fill,minmax(104px,1fr));gap:7px}.thumb{height:62px}}
  </style>
</head>
<body>
  <main class="shell">
    <section class="hero">
      <div>
        <h1>The Face Tomb</h1>
        <p class="subtitle">A threshold directory for the tomb project: enter the working Three.js chamber, inspect the surrounding files, and maintain the living punch list for future rooms, doors, rituals, and mistakes we vow not to repeat.</p>
        <div class="nav">
          <a class="button" href="app/">Enter App</a>
          <a class="button" href="editor/">Open Editor</a>
          <a class="button" href="api/scene.php">Scene API</a>
          <a class="button" href="images/">Images Crypt</a>
        </div>
      </div>
      <div class="seal">THE FACE<br>TOMB</div>
    </section>

    <section class="panel punchPanel">
      <h2>Room & Feature Punch List</h2>
      <div class="punchTop">
        <form class="punchForm" id="punchForm">
          <textarea id="newText" placeholder="Write a feature, room, bug, or ominous requirement…"></textarea>
          <div class="row">
            <input id="newRoom" list="roomOptions" placeholder="Room / category, e.g. Doors" />
            <button type="submit">Inscribe</button>
          </div>
          <datalist id="roomOptions">
            <option value="Outer Chamber"></option><option value="Doors"></option><option value="Movement"></option><option value="Future Rooms"></option><option value="Dashboard"></option><option value="Images"></option>
          </datalist>
          <div id="status" class="status"></div>
        </form>
        <div class="empty">The punch list now lives above the chambers so it cannot hide in the crypt-wall margins.</div>
      </div>
      <div id="punchItems" class="items"></div>
    </section>

    <section class="panel">
      <h2>Directory of Chambers & Relics</h2>
      <div class="grid">
        <?php foreach ($directoryItems as $entry): ?>
          <a class="card" href="<?= $entry['href'] ?>">
            <div class="thumb"><?= $entry['thumb'] ?></div>
            <div class="cardBody">
              <div class="kind"><?= htmlspecialchars($entry['kind'], ENT_QUOTES, 'UTF-8') ?></div>
              <div class="name"><?= $entry['label'] ?></div>
              <div class="mtime"><?= htmlspecialchars($entry['mtime'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <script>
    let punchItems = <?= $punchJson ?: '[]' ?>;
    const list = document.getElementById('punchItems');
    const form = document.getElementById('punchForm');
    const status = document.getElementById('status');
    const newText = document.getElementById('newText');
    const newRoom = document.getElementById('newRoom');

    function escapeHtml(value){return String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));}
    function displayDate(value){const d = new Date(value || Date.now()); return Number.isNaN(d.getTime()) ? 'just now' : d.toLocaleString();}
    function renderPunchList(){
      const sorted = [...punchItems].sort((a,b) => Number(a.done) - Number(b.done));
      if(!sorted.length){list.innerHTML = '<div class="empty">No inscriptions yet. The tomb awaits instruction.</div>';return;}
      list.innerHTML = sorted.map(item => `
        <article class="punch ${item.done ? 'done' : ''}" data-id="${escapeHtml(item.id)}">
          <input class="check" type="checkbox" ${item.done ? 'checked' : ''} aria-label="Mark complete">
          <div><span class="room">${escapeHtml(item.room || 'General')}</span><div class="punchText" contenteditable="true" spellcheck="true">${escapeHtml(item.text)}</div><div class="meta">Updated ${escapeHtml(displayDate(item.updated || item.created))}</div></div>
          <button class="mini delete" type="button">Delete</button>
        </article>`).join('');
    }
    async function send(action, payload = {}){
      status.textContent = 'Writing to punchlist.json…';
      const response = await fetch(location.href, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action, ...payload})});
      const data = await response.json();
      if(!data.ok){ throw new Error(data.error || 'Unknown tomb-writing error.'); }
      punchItems = data.items || [];
      renderPunchList();
      status.textContent = 'Saved.';
      setTimeout(() => { if(status.textContent === 'Saved.') status.textContent = ''; }, 1400);
    }
    form.addEventListener('submit', async event => {event.preventDefault();const text = newText.value.trim();const room = newRoom.value.trim() || 'General';if(!text) return;try{await send('add', {text, room});newText.value = '';}catch(error){status.textContent = error.message;}});
    list.addEventListener('change', async event => {const card = event.target.closest('.punch');if(!card || !event.target.classList.contains('check')) return;try{await send('toggle', {id: card.dataset.id});}catch(error){status.textContent = error.message;renderPunchList();}});
    list.addEventListener('click', async event => {const card = event.target.closest('.punch');if(!card || !event.target.classList.contains('delete')) return;try{await send('delete', {id: card.dataset.id});}catch(error){status.textContent = error.message;}});
    list.addEventListener('focusout', async event => {const textEl = event.target.closest('.punchText');const card = event.target.closest('.punch');if(!textEl || !card) return;const item = punchItems.find(i => i.id === card.dataset.id);const text = textEl.textContent.trim();if(!item || !text || text === item.text) return;try{await send('edit', {id:item.id, text, room:item.room || 'General'});}catch(error){status.textContent = error.message;renderPunchList();}});
    renderPunchList();
  </script>
</body>
</html>
