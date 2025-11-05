<?php
// -------------------------------------------
// "Juri in B:" – Minimal Mobile Version
// Countdown bis Ankunft, dann grüne Hochzählung
// -------------------------------------------

$ticketDir = __DIR__ . '/tickets';
$pdfs = glob($ticketDir . '/*.pdf');

if (!$pdfs) {
    http_response_code(200);
    echo "<!doctype html><meta charset='utf-8'><style>
            body{background:#000;color:#ff0;font:16px monospace;
                 margin:0;display:grid;place-items:center;height:100vh}
          </style><body>
          Keine PDF-Tickets im Ordner <code>tickets/</code> gefunden.
          </body>";
    exit;
}

rsort($pdfs, SORT_NATURAL | SORT_FLAG_CASE);
$file = $pdfs[0];

// --- PDF zu Text (benötigt: sudo apt install poppler-utils)
$tmpTxt = tempnam(sys_get_temp_dir(), 'pdf_');
@exec("pdftotext -layout " . escapeshellarg($file) . " " . escapeshellarg($tmpTxt));
$text = @file_get_contents($tmpTxt) ?: '';
@unlink($tmpTxt);

// --- Ankunftszeit suchen
$ankunftStr = '00:00';
if (preg_match('/\b(?:Ankunft|an)\b[^\d]{0,10}([0-2]\d:[0-5]\d)/iu', $text, $m)) {
    $ankunftStr = $m[1];
}

// --- Zielzeit bestimmen
$heute = date('Y-m-d');
$ankunftTs = strtotime("$heute $ankunftStr");
if ($ankunftTs !== false && $ankunftTs < time()) {
    $ankunftTs += 86400;
}
$diffSekunden = max(-86400, ($ankunftTs ?: time()) - time());
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Juri in B:</title>
<style>
:root{
  --bg:#000;
  --title:#ffeb3b; /* Gelb */
  --down:#ff3b30;  /* Rot */
  --up:#00e676;    /* Grün */
  --link:#b388ff;  /* Lila */
}
*{box-sizing:border-box}
html,body{
  height:100%;
  margin:0;
  background:var(--bg);
  color:var(--title);
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
}
.wrapper{
  min-height:100%;
  padding: max(env(safe-area-inset-top), 2vh)
           max(env(safe-area-inset-right), 3vw)
           max(env(safe-area-inset-bottom), 3vh)
           max(env(safe-area-inset-left), 3vw);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:space-between;
}
h1{
  margin:0;
  font-weight:700;
  font-size: clamp(28px, 8vw, 64px);
  text-align:center;
}
#count{
  font-size: clamp(48px, 18vw, 180px);
  font-variant-numeric: tabular-nums;
  color: var(--down);
  transition: color .25s ease;
  user-select:none;
  line-height:1;
  text-align:center;
}
.tickets-link{
  text-align:center;
  padding-bottom:max(env(safe-area-inset-bottom),2vh);
}
.tickets-link a{
  color:var(--link);
  font-size: clamp(12px, 3vw, 16px);
  text-decoration:none;
}
.tickets-link a:hover{
  text-decoration:underline;
}
</style>
<script>
let seconds = <?= (int)$diffSekunden ?>;
function tick(){
  const el = document.getElementById('count');
  if (!el) return;
  if (seconds >= 0) {
    el.style.color = getComputedStyle(document.documentElement)
                      .getPropertyValue('--down').trim();
    el.textContent = seconds;
  } else {
    el.style.color = getComputedStyle(document.documentElement)
                      .getPropertyValue('--up').trim();
    el.textContent = "+" + Math.abs(seconds);
  }
  seconds--;
  setTimeout(tick, 1000);
}
window.addEventListener('load', tick);
</script>
</head>
<body>
  <div class="wrapper">
    <h1>Juri in B:</h1>
    <div id="count"><?= (int)$diffSekunden ?></div>
    <div class="tickets-link">
      <a href="tickets/">tickets</a>
    </div>
  </div>
</body>
</html>
