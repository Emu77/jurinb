<?php
// tickets/index.php – mittige Ticketliste, öffnet PDFs in neuem Tab

$dir = __DIR__;
$files = glob($dir . '/*.pdf');
sort($files, SORT_NATURAL | SORT_FLAG_CASE); // ältestes zuerst

function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Tickets</title>
<style>
:root{
  --bg:#000;
  --title:#ffeb3b;
  --link:#b388ff;
  --row:#0b0b0b;
}
*{box-sizing:border-box}
html,body{
  margin:0;
  height:100%;
  background:var(--bg);
  color:var(--title);
  font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,"Liberation Mono",monospace;
}
.wrapper{
  min-height:100%;
  padding:max(env(safe-area-inset-top),3vh)
          max(env(safe-area-inset-right),3vw)
          max(env(safe-area-inset-bottom),4vh)
          max(env(safe-area-inset-left),3vw);
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  align-items:center;
}
h1{
  margin:0;
  font-size:clamp(24px,7vw,52px);
  text-align:center;
}
.list{
  margin-top:3vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:12px;
  width:100%;
  max-width:600px;
}
.item{
  width:100%;
  text-align:center;
  padding:12px 14px;
  background:var(--row);
  border:1px solid #151515;
  border-radius:10px;
}
a.file{
  color:var(--link);
  text-decoration:none;
  display:inline-block;
  max-width:90%;
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;
}
a.file:hover{text-decoration:underline;}
.back{
  text-align:center;
  margin-top:4vh;
}
.back a{
  color:var(--link);
  text-decoration:none;
  font-size:clamp(12px,3vw,16px);
}
.back a:hover{text-decoration:underline;}
.empty{
  opacity:.7;
  text-align:center;
  margin-top:10vh;
}
</style>
</head>
<body>
  <div class="wrapper">
    <div>
      <h1>Tickets</h1>

      <?php if (!$files): ?>
        <div class="empty">Keine PDF-Tickets gefunden.</div>
      <?php else: ?>
        <div class="list">
          <?php foreach ($files as $f): ?>
            <div class="item">
              <a class="file"
                 href="<?= rawurlencode(basename($f)) ?>"
                 target="_blank" rel="noopener">
                <?= h(basename($f)) ?>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="back">
      <a href="../">← zurück</a>
    </div>
  </div>
</body>
</html>
