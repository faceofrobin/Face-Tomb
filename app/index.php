<?php
$html = file_get_contents(__DIR__ . '/index.html');

// inject UI container after tools
$html = str_replace('</div>\n<div id="mobileControls"', "</div>\n<div id=\"roomQuick\" style=\"position:fixed;top:60px;right:12px;z-index:7;display:flex;flex-direction:column;gap:6px;max-width:160px\"></div>\n<div id=\"mobileControls\"", $html);

// inject JS into module before init()
$inject = <<<JS
const roomQuick = document.getElementById('roomQuick');

function buildRoomShortcuts(){
  if(!roomQuick) return;
  roomQuick.innerHTML='';
  (sceneData.rooms||[]).forEach(r=>{
    const b=document.createElement('button');
    b.className='pill';
    b.textContent=(r.title||r.id);
    b.onclick=(e)=>{ e.stopPropagation(); loadRoom(r.id); };
    roomQuick.appendChild(b);
  });
}

const __origInit = init;
init = async function(){
  await __origInit();
  buildRoomShortcuts();
};
JS;

$html = str_replace("init();\n</script>", $inject . "\ninit();\n</script>", $html);

echo $html;
