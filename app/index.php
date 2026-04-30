<?php
$html = file_get_contents(__DIR__ . '/index.html');

// inject UI container after tools
$html = str_replace('</div>\n<div id="mobileControls"', "</div>\n<div id=\"roomQuick\" style=\"position:fixed;top:60px;right:12px;z-index:7;display:flex;flex-direction:column;gap:6px;max-width:160px\"></div>\n<div id=\"mobileControls\"", $html);

// make the glass chapel obey the big tower config instead of hard-coded 12-unit walls
$oldGlass = <<<'JS'
function buildGlass(room){reset();state.room='glass';const c=room.config||{},towerRadius=c.towerRadius||10,roomScale=c.roomScale||2,roomHalf=towerRadius*roomScale,sides=c.sides||12,layers=c.layers||7,panelW=c.panelWidth||5.2,panelH=c.panelHeight||4.2,gap=c.verticalGap||3.8,baseY=c.baseAtEyeLevel?BASE_EYE:0;scene.background=new THREE.Color(0xf7f5ef);scene.fog=new THREE.FogExp2(0xf7f5ef,.006);setSpawn(room.spawn?.x??0,room.spawn?.z??roomHalf+2,cfg('glassSpawnYaw'));const media=(sceneData.media?.[room.category]||[]).filter(i=>i.type==='image');box(0,-.06,0,roomHalf*2,.12,roomHalf*2,new THREE.MeshStandardMaterial({color:0xfaf8ee,roughness:.78}),false);box(0,6,-roomHalf,roomHalf*2,12,.28,mats.white,true);box(0,6,roomHalf,roomHalf*2,12,.28,mats.white,true);box(-roomHalf,6,0,.28,12,roomHalf*2,mats.white,true);box(roomHalf,6,0,.28,12,roomHalf*2,mats.white,true);box(0,12,0,roomHalf*2,.2,roomHalf*2,new THREE.MeshStandardMaterial({color:0xffffff,roughness:.9}),false);const apse=new THREE.Mesh(new THREE.SphereGeometry(roomHalf,48,16,0,Math.PI*2,0,Math.PI/2),new THREE.MeshStandardMaterial({color:0xffffff,roughness:.88,side:THREE.BackSide}));apse.scale.y=.28;apse.position.y=11.9;root.add(apse);const tower=new THREE.Group();root.add(tower);for(let layer=0;layer<layers;layer++){for(let i=0;i<sides;i++){const a=i*Math.PI*2/sides,x=Math.sin(a)*towerRadius,z=Math.cos(a)*towerRadius,item=media[(i+layer*sides)%Math.max(media.length,1)];const material=item?new THREE.MeshBasicMaterial({map:tex(item.url),side:THREE.DoubleSide}):mats.glass;const p=new THREE.Mesh(new THREE.PlaneGeometry(panelW,panelH),material);p.position.set(x,baseY+layer*gap,z);p.rotation.y=a;tower.add(p);addPanelFrame(tower,x,baseY+layer*gap,z,a,panelW,panelH)}}animated.push({object:tower,speed:c.spinSpeed||.055});addLight(new THREE.HemisphereLight(0xffffff,0xbfdfff,1.35));addLight(new THREE.PointLight(0xffffff,4.2,roomHalf*3,1.4)).position.set(0,8,0);addLight(new THREE.PointLight(0x9edbff,2.4,roomHalf*2,2)).position.set(0,baseY+layers*gap*.55,0);if(room.audio)playRoomAudio(room.audio,.14);const ret=new THREE.Mesh(new THREE.OctahedronGeometry(.9,0),mats.glow);ret.position.set(0,1.5,roomHalf-3);root.add(ret);clickObj(ret,'Return Relic','Back to the main chamber.',buildMain);animated.push({object:ret,speed:.8});stats.textContent=`${room.title} · ${media.length} glass images`;msg.textContent=media.length?`The Glass Chapel is open with ${media.length} images on the tower.`:'The Glass Chapel is open, but no GLASS images were found.'}
JS;

$newGlass = <<<'JS'
function buildGlass(room){reset();state.room='glass';const c=room.config||{},towerRadius=c.towerRadius||12,roomScale=c.roomScale||2.35,roomHalf=towerRadius*roomScale,sides=c.sides||12,layers=c.layers||3,panelW=c.panelWidth||3.2,panelH=c.panelHeight||8,gap=c.verticalGap||8.15,baseY=c.baseAtEyeLevel?BASE_EYE:0,wallH=c.wallHeight||30,roofClearance=c.roofClearance||5.7,roofY=wallH+roofClearance;scene.background=new THREE.Color(0xf7f5ef);scene.fog=new THREE.FogExp2(0xf7f5ef,.0045);setSpawn(room.spawn?.x??0,room.spawn?.z??roomHalf-1,cfg('glassSpawnYaw'));const media=(sceneData.media?.[room.category]||[]).filter(i=>i.type==='image');box(0,-.06,0,roomHalf*2,.12,roomHalf*2,new THREE.MeshStandardMaterial({color:0xfaf8ee,roughness:.78}),false);box(0,wallH/2,-roomHalf,roomHalf*2,wallH,.28,mats.white,true);box(0,wallH/2,roomHalf,roomHalf*2,wallH,.28,mats.white,true);box(-roomHalf,wallH/2,0,.28,wallH,roomHalf*2,mats.white,true);box(roomHalf,wallH/2,0,.28,wallH,roomHalf*2,mats.white,true);box(0,roofY,0,roomHalf*2,.24,roomHalf*2,new THREE.MeshStandardMaterial({color:0xffffff,roughness:.9}),false);const apse=new THREE.Mesh(new THREE.SphereGeometry(roomHalf,64,18,0,Math.PI*2,0,Math.PI/2),new THREE.MeshStandardMaterial({color:0xffffff,roughness:.88,side:THREE.BackSide}));apse.scale.y=.32;apse.position.y=roofY-.1;root.add(apse);const tower=new THREE.Group();root.add(tower);for(let layer=0;layer<layers;layer++){for(let i=0;i<sides;i++){const a=i*Math.PI*2/sides,x=Math.sin(a)*towerRadius,z=Math.cos(a)*towerRadius,y=baseY+layer*gap,item=media[(i+layer*sides)%Math.max(media.length,1)];const material=item?new THREE.MeshBasicMaterial({map:tex(item.url),side:THREE.DoubleSide}):mats.glass;const p=new THREE.Mesh(new THREE.PlaneGeometry(panelW,panelH),material);p.position.set(x,y,z);p.rotation.y=a;tower.add(p);addPanelFrame(tower,x,y,z,a,panelW,panelH)}}animated.push({object:tower,speed:c.spinSpeed||.04});addLight(new THREE.HemisphereLight(0xffffff,0xbfdfff,1.55));addLight(new THREE.PointLight(0xffffff,5.2,roomHalf*3.2,1.25)).position.set(0,wallH*.55,0);addLight(new THREE.PointLight(0x9edbff,3.1,roomHalf*2.4,1.7)).position.set(0,baseY+layers*gap*.5,0);if(room.audio)playRoomAudio(room.audio,.14);const ret=new THREE.Mesh(new THREE.OctahedronGeometry(.9,0),mats.glow);ret.position.set(0,1.5,roomHalf-3);root.add(ret);clickObj(ret,'Return Relic','Back to the main chamber.',buildMain);animated.push({object:ret,speed:.8});stats.textContent=`${room.title} · ${media.length} huge glass images · roof ${roofY.toFixed(1)}`;msg.textContent=media.length?`The Glass Chapel is huge: ${layers} tall rings under a raised roof.`:'The Glass Chapel is huge, but no GLASS images were found.'}
JS;

$html = str_replace($oldGlass, $newGlass, $html);

// add fade.jpeg as a clipped circular image medallion inside the main dome
$oldMainDome = <<<'JS'
const dome=new THREE.Mesh(new THREE.SphereGeometry(22.8,48,20,0,Math.PI*2,0,Math.PI/2),mats.wall);dome.scale.y=.38;dome.position.y=10.9;root.add(dome);
JS;

$newMainDome = <<<'JS'
const dome=new THREE.Mesh(new THREE.SphereGeometry(22.8,48,20,0,Math.PI*2,0,Math.PI/2),mats.wall);dome.scale.y=.38;dome.position.y=10.9;root.add(dome);const fadeTex=tex('./images/fade.jpeg');fadeTex.center.set(.5,.5);const fadeMedallion=new THREE.Mesh(new THREE.CircleGeometry(7.25,96),new THREE.MeshBasicMaterial({map:fadeTex,side:THREE.DoubleSide}));fadeMedallion.rotation.x=-Math.PI/2;fadeMedallion.position.set(0,10.82,0);root.add(fadeMedallion);const fadeRing=new THREE.Mesh(new THREE.TorusGeometry(7.32,.12,12,128),mats.gold);fadeRing.rotation.x=-Math.PI/2;fadeRing.position.set(0,10.815,0);root.add(fadeRing);
JS;

$html = str_replace($oldMainDome, $newMainDome, $html);

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
