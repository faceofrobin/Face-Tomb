<?php

function patchArtHistoryRenderer(string $html): string
{
    $old = <<<'JS'
function buildArtHistory(room){
JS;

    $new = <<<'JS'
function buildArtHistory(room){
  reset();state.room='art-history';scene.background=new THREE.Color(0x050403);scene.fog=new THREE.FogExp2(0x050403,.008);
  const c=room.config||{};
  const mobile=window.matchMedia('(hover:none),(pointer:coarse)').matches;
  const roomW=c.roomWidth||20,roomD=c.roomDepth||12,roomH=c.roomHeight||8,hallW=c.hallWidth||4,passageW=Math.min(c.passageWidth||3.4,roomD-1),artW=c.artWidth||4,artH=c.artHeight||3,benchH=c.benchHeight||.62,spotI=c.spotlightBrightness||1.45,spotD=c.spotlightDistance||12,artY=c.artCenterHeight||4.4;
  const lazyRadius=mobile?(c.mobileLazyRadius||20):(c.lazyRadius||28);
  const maxLoaded=mobile?(c.mobileMaxLoadedArt||6):(c.maxLoadedArt||10);
  const useSpotlights=!mobile||c.mobileSpotlights===true;
  setSpawn(room.spawn?.x??0,room.spawn?.z??0,Number(room.spawn?.yaw??90));
  const all=(sceneData.media?.[room.category]||[]).filter(i=>i.type==='image');
  const wallItem=all.find(i=>(i.filename||'').toLowerCase()==='wallpaper.png')||all.find(i=>/^wallpaper\./i.test(i.filename||''))||all.find(i=>/^(wall|background|texture)(\.|-|_)/i.test(i.filename||''));
  const media=all.filter(i=>i!==wallItem);
  function wallpaperMaterial(){if(!wallItem)return mats.wall;const wt=tex(wallItem.url,c.wallRepeatX||4,c.wallRepeatY||2);wt.colorSpace=THREE.SRGBColorSpace;return new THREE.MeshBasicMaterial({map:wt,side:THREE.DoubleSide})}
  const wallMat=wallpaperMaterial();
  function titleCanvas(text){const cn=document.createElement('canvas');cn.width=mobile?512:1024;cn.height=mobile?96:192;const g=cn.getContext('2d');g.fillStyle='rgba(0,0,0,.82)';g.fillRect(0,0,cn.width,cn.height);g.strokeStyle='#e7d29a';g.lineWidth=mobile?4:8;g.strokeRect(5,5,cn.width-10,cn.height-10);g.fillStyle='#e7d29a';g.font=`bold ${mobile?27:54}px Arial`;g.textAlign='center';g.textBaseline='middle';g.fillText(String(text||'UNTITLED').replace(/[-_]+/g,' ').toUpperCase().slice(0,34),cn.width/2,cn.height/2);return new THREE.CanvasTexture(cn)}
  function museumBench(x,z,rot,parent){const g=new THREE.Group();box(0,benchH,0,2.5,.22,.6,mats.stone,false,g);box(-1,benchH/2,0,.22,benchH,.6,mats.stone,false,g);box(1,benchH/2,0,.22,benchH,.6,mats.stone,false,g);g.position.set(x,0,z);g.rotation.y=rot;parent.add(g);return g}
  const artNodes=[],activeArt=new Map();let index=0,chain=0;
  while(index<media.length||chain===0){
    const gx=chain*(roomW+hallW),group=new THREE.Group();group.position.x=gx;root.add(group);
    box(0,-.05,0,roomW,.1,roomD,mats.floor,false,group);box(0,roomH,0,roomW,.15,roomD,mats.ceil,false,group);
    box(0,roomH/2,-roomD/2,roomW,roomH,.3,wallMat,true,group);box(0,roomH/2,roomD/2,roomW,roomH,.3,wallMat,true,group);
    const sideSeg=(roomD-passageW)/2;
    box(-roomW/2,roomH/2,-(passageW/2+sideSeg/2),.3,roomH,sideSeg,wallMat,true,group);box(-roomW/2,roomH/2,passageW/2+sideSeg/2,.3,roomH,sideSeg,wallMat,true,group);
    const hasNext=index+6<media.length;
    if(hasNext){box(roomW/2,roomH/2,-(passageW/2+sideSeg/2),.3,roomH,sideSeg,wallMat,true,group);box(roomW/2,roomH/2,passageW/2+sideSeg/2,.3,roomH,sideSeg,wallMat,true,group)}else{box(roomW/2,roomH/2,0,.3,roomH,roomD,wallMat,true,group)}
    for(let i=0;i<6;i++){
      if(!media[index])break;const item=media[index],front=i<3,x=(i%3-1)*(roomW/3.4),z=front?-roomD/2+.19:roomD/2-.19;
      artNodes.push({index,position:new THREE.Vector3(gx+x,artY,z),rotation:front?0:Math.PI,item});
      if(useSpotlights){const target=new THREE.Object3D();target.position.set(x,artY,z);group.add(target);const spot=new THREE.SpotLight(0xffe8b8,spotI,spotD,.5,.55,1.3);spot.position.set(x,roomH-.45,z+(front?2.4:-2.4));spot.target=target;group.add(spot)}
      if(i%2===1)museumBench(x-(roomW/6.8),front?-2.9:2.9,front?0:Math.PI,group);
      index++;
    }
    if(hasNext){const hx=roomW/2+hallW/2;box(hx,-.05,0,hallW,.1,passageW,mats.floor,false,group);box(hx,roomH,0,hallW,.15,passageW,mats.ceil,false,group);box(hx,roomH/2,-passageW/2,hallW,roomH,.3,wallMat,true,group);box(hx,roomH/2,passageW/2,hallW,roomH,.3,wallMat,true,group)}
    chain++;
  }
  function buildArtMesh(node){const frame=new THREE.Group();frame.position.copy(node.position);frame.rotation.y=node.rotation;const back=new THREE.Mesh(new THREE.BoxGeometry(artW+.35,artH+.35,.18),new THREE.MeshBasicMaterial({color:0x000000}));const art=new THREE.Mesh(new THREE.PlaneGeometry(artW,artH),new THREE.MeshBasicMaterial({map:tex(node.item.url),side:THREE.DoubleSide}));art.position.z=.1;frame.add(back,art);const label=new THREE.Mesh(new THREE.PlaneGeometry(artW,Math.max(.45,artH*.18)),new THREE.MeshBasicMaterial({map:titleCanvas(node.item.title),transparent:true,side:THREE.DoubleSide}));label.position.set(0,-artH/2-.55,.11);frame.add(label);root.add(frame);return frame}
  function disposeArt(mesh){root.remove(mesh);mesh.traverse(o=>{o.geometry?.dispose?.();const a=Array.isArray(o.material)?o.material:[o.material];a.filter(Boolean).forEach(m=>{if(m.map)m.map.dispose?.();m.dispose?.()})})}
  let lastLazyUpdate=0;
  function updateArtLoading(){if(state.room!=='art-history')return;const now=performance.now();if(now-lastLazyUpdate<(mobile?350:180))return;lastLazyUpdate=now;const nearby=artNodes.map(n=>({node:n,dist:n.position.distanceTo(camera.position)})).filter(n=>n.dist<=lazyRadius).sort((a,b)=>a.dist-b.dist).slice(0,maxLoaded);const needed=new Set(nearby.map(n=>n.node.index));nearby.forEach(({node})=>{if(!activeArt.has(node.index))activeArt.set(node.index,buildArtMesh(node))});for(const [id,mesh] of activeArt){if(!needed.has(id)){disposeArt(mesh);activeArt.delete(id)}}stats.textContent=`${room.title} · ${activeArt.size}/${media.length} works loaded${mobile?' · mobile mode':''}`}
  if(!window.__faceTombArtTick){window.__faceTombArtTick=[];const oldMove=move;move=function(dt){oldMove(dt);window.__faceTombArtTick.forEach(fn=>fn())}}
  window.__faceTombArtTick=[updateArtLoading];
  addLight(new THREE.HemisphereLight(0xe8d8b8,0x080604,mobile?.9:.72));if(!mobile)addLight(new THREE.PointLight(0xffd9a0,.9,60,2)).position.set(0,5,0);
  const ret=new THREE.Mesh(new THREE.OctahedronGeometry(.8,0),mats.glow);ret.position.set(0,1.5,roomD/2-2);root.add(ret);clickObj(ret,'Return','Back to the main chamber.',buildMain);animated.push({object:ret,speed:.8});
  updateArtLoading();msg.textContent=media.length?`The Art History Room loaded nearby works from /AIFACES/${room.category}.`:`No art found in /AIFACES/${room.category}.`;
}
JS;

    $start = strpos($html, $old);
    if ($start === false) {
        return $html;
    }

    $endMarker = "\nJS;\n\n\$html = str_replace(\"function loadRoom(id){\"";
    $end = strpos($html, $endMarker, $start);
    if ($end === false) {
        return $html;
    }

    return substr($html, 0, $start) . $new . substr($html, $end);
}
