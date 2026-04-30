<?php
$html = file_get_contents(__DIR__ . '/index.html');

// Restore fade.jpeg as a curved medallion in the main dome.
$baseDome = <<<'JS'
const dome=new THREE.Mesh(new THREE.SphereGeometry(22.8,48,20,0,Math.PI*2,0,Math.PI/2),mats.wall);dome.scale.y=.38;dome.position.y=10.9;root.add(dome);
JS;
$domeFace = <<<'JS'
const dome=new THREE.Mesh(new THREE.SphereGeometry(22.8,48,20,0,Math.PI*2,0,Math.PI/2),mats.wall);dome.scale.y=.38;dome.position.y=10.9;root.add(dome);function curvedFadeDisk(radius=10.8,sag=.82,rings=28,segs=128){const pos=[],uv=[],idx=[];for(let r=0;r<=rings;r++){const rn=r/rings,rr=radius*rn,y=sag*(1-rn*rn);for(let s=0;s<=segs;s++){const a=s/segs*Math.PI*2,x=Math.cos(a)*rr,z=Math.sin(a)*rr;pos.push(x,y,z);uv.push(.5+x/(radius*2),.5-z/(radius*2))}}for(let r=0;r<rings;r++){for(let s=0;s<segs;s++){const a=r*(segs+1)+s,b=a+segs+1;idx.push(a,b,a+1,b,b+1,a+1)}}const g=new THREE.BufferGeometry();g.setAttribute('position',new THREE.Float32BufferAttribute(pos,3));g.setAttribute('uv',new THREE.Float32BufferAttribute(uv,2));g.setIndex(idx);g.computeVertexNormals();return g}const fadeTex=tex('./images/fade.jpeg');fadeTex.center.set(.5,.5);fadeTex.colorSpace=THREE.SRGBColorSpace;const fadeCap=new THREE.Mesh(curvedFadeDisk(10.8,.82,28,128),new THREE.MeshBasicMaterial({map:fadeTex,side:THREE.DoubleSide,transparent:false,depthWrite:true}));fadeCap.position.y=8.62;root.add(fadeCap);const fadeGlow=new THREE.PointLight(0xffe7b0,1.65,24,2);fadeGlow.position.set(0,8.25,0);addLight(fadeGlow);const fadeRing=new THREE.Mesh(new THREE.TorusGeometry(10.8,.15,12,180),mats.gold);fadeRing.rotation.x=-Math.PI/2;fadeRing.position.set(0,8.62,0);root.add(fadeRing);
JS;
$html = str_replace($baseDome, $domeFace, $html);

// Make the floorplan relic a purple pyramid in the main chamber.
$oldRelicGeo = <<<'JS'
const geo=r.kind==='bar-room'?new THREE.CylinderGeometry(.34,.52,1.7,32):r.kind==='glass-tower'?new THREE.OctahedronGeometry(.85,0):new THREE.TorusKnotGeometry(.56,.14,72,10);const material=r.kind==='bar-room'?new THREE.MeshPhysicalMaterial({color:0xb06b24,transparent:true,opacity:.65,roughness:.2}):r.kind==='glass-tower'?mats.glass:mats.glow;
JS;
$newRelicGeo = <<<'JS'
const geo=(r.kind==='floorplan-gallery'||r.kind==='floorplan gallery')?new THREE.ConeGeometry(.95,1.75,4):r.kind==='bar-room'?new THREE.CylinderGeometry(.34,.52,1.7,32):r.kind==='glass-tower'?new THREE.OctahedronGeometry(.85,0):new THREE.TorusKnotGeometry(.56,.14,72,10);const material=(r.kind==='floorplan-gallery'||r.kind==='floorplan gallery')?new THREE.MeshStandardMaterial({color:0x7b2cff,emissive:0x2a005e,emissiveIntensity:.55,roughness:.42}):r.kind==='bar-room'?new THREE.MeshPhysicalMaterial({color:0xb06b24,transparent:true,opacity:.65,roughness:.2}):r.kind==='glass-tower'?mats.glass:mats.glow;
JS;
$html = str_replace($oldRelicGeo, $newRelicGeo, $html);

$floorplanJs = <<<'JS'
async function buildFloorplan(room){
  const c=room.config||{},H=c.wallHeight||5.8,T=c.wallThickness||.38,scale=c.svgPixelsPerUnit||12,mapUrl=c.mapUrl||'./maps/floorplan.svg';
  try{
    const res=await fetch(mapUrl+'?ts='+Date.now());
    if(!res.ok)throw new Error('missing svg');
    const doc=new DOMParser().parseFromString(await res.text(),'image/svg+xml'),svg=doc.querySelector('svg');
    if(!svg)throw new Error('bad svg');
    reset();state.room='floorplan';scene.background=new THREE.Color(0x060606);scene.fog=new THREE.FogExp2(0x060606,.012);
    const vb=(svg.getAttribute('viewBox')||`0 0 ${svg.getAttribute('width')||1000} ${svg.getAttribute('height')||800}`).trim().split(/[ ,]+/).map(Number),vx=vb[0]||0,vy=vb[1]||0,vw=vb[2]||1000,vh=vb[3]||800;
    const X=x=>(Number(x)-vx-vw/2)/scale,Z=y=>(Number(y)-vy-vh/2)/scale,W=w=>Math.max(Number(w)/scale,.05);
    const fill=e=>(e.getAttribute('fill')||'').toLowerCase(), stroke=e=>(e.getAttribute('stroke')||'').toLowerCase(), cls=e=>(e.getAttribute('class')||'').toLowerCase();
    const erasers=[...doc.querySelectorAll('rect')].filter(e=>fill(e)==='white'&&(stroke(e)==='none'||!stroke(e))).map(e=>({x:+e.getAttribute('x'),y:+e.getAttribute('y'),w:+e.getAttribute('width'),h:+e.getAttribute('height')}));
    const erased=(x1,y1,x2,y2)=>erasers.some(r=>{const mx=(x1+x2)/2,my=(y1+y2)/2;return mx>=r.x&&mx<=r.x+r.w&&my>=r.y&&my<=r.y+r.h});
    function floorRect(e){const x=+e.getAttribute('x'),y=+e.getAttribute('y'),w=+e.getAttribute('width'),h=+e.getAttribute('height');box(X(x+w/2),-.05,Z(y+h/2),W(w),.1,W(h),mats.floor,false)}
    function wallSeg(x1,y1,x2,y2){if(erased(x1,y1,x2,y2))return;const xA=X(x1),zA=Z(y1),xB=X(x2),zB=Z(y2),L=Math.hypot(xB-xA,zB-zA);if(L<.08)return;const mx=(xA+xB)/2,mz=(zA+zB)/2,a=Math.atan2(zB-zA,xB-xA);const m=new THREE.Mesh(new THREE.BoxGeometry(L,H,T),mats.wall);m.position.set(mx,H/2,mz);m.rotation.y=-a;root.add(m);colliders.push({x:mx,z:mz,w:Math.abs(Math.cos(a))*L+T,d:Math.abs(Math.sin(a))*L+T})}
    function floorPoly(points){const sh=new THREE.Shape();points.forEach((p,i)=>i?sh.lineTo(X(p[0]),Z(p[1])):sh.moveTo(X(p[0]),Z(p[1])));sh.closePath();const mesh=new THREE.Mesh(new THREE.ShapeGeometry(sh),mats.floor);mesh.rotation.x=-Math.PI/2;root.add(mesh)}
    function pts(e){return(e.getAttribute('points')||'').trim().split(/\s+/).map(p=>p.split(',').map(Number)).filter(p=>p.length===2&&Number.isFinite(p[0])&&Number.isFinite(p[1]))}
    let spawnSet=false,artCount=0;
    [...doc.querySelectorAll('rect')].forEach(e=>{const x=+e.getAttribute('x'),y=+e.getAttribute('y'),w=+e.getAttribute('width'),h=+e.getAttribute('height'),f=fill(e),s=stroke(e);if(f==='blue'||cls(e).includes('art')){const p=new THREE.Mesh(new THREE.PlaneGeometry(2.2,2.8),mats.glow);p.position.set(X(x+w/2),2.6,Z(y+h/2));root.add(p);artCount++;return}if(f==='white'){floorRect(e);if(s==='black'){wallSeg(x,y,x+w,y);wallSeg(x+w,y,x+w,y+h);wallSeg(x+w,y+h,x,y+h);wallSeg(x,y+h,x,y)}}});
    [...doc.querySelectorAll('polygon')].forEach(e=>{const p=pts(e),f=fill(e);if(!p.length)return;if(f==='white'){floorPoly(p);for(let i=0;i<p.length;i++){const a=p[i],b=p[(i+1)%p.length];wallSeg(a[0],a[1],b[0],b[1])}}else if(f==='black'){const g=new THREE.Shape();p.forEach((q,i)=>i?g.lineTo(X(q[0]),Z(q[1])):g.moveTo(X(q[0]),Z(q[1])));g.closePath();const m=new THREE.Mesh(new THREE.ShapeGeometry(g),new THREE.MeshStandardMaterial({color:0x111111,emissive:0x7b2cff,emissiveIntensity:.35}));m.rotation.x=-Math.PI/2;m.position.y=.03;root.add(m)}});
    [...doc.querySelectorAll('line')].forEach(e=>{if(stroke(e)==='black')wallSeg(+e.getAttribute('x1'),+e.getAttribute('y1'),+e.getAttribute('x2'),+e.getAttribute('y2'))});
    [...doc.querySelectorAll('circle')].forEach(e=>{if(fill(e)==='red'||cls(e).includes('spawn')){setSpawn(X(+e.getAttribute('cx')),Z(+e.getAttribute('cy')),Number(e.getAttribute('data-yaw')||0));spawnSet=true}});
    if(!spawnSet)setSpawn(room.spawn?.x??0,room.spawn?.z??0,0);
    const ret=new THREE.Mesh(new THREE.OctahedronGeometry(.75,0),mats.glow);ret.position.set(camera.position.x,1.3,camera.position.z+2.5);root.add(ret);clickObj(ret,'Return','Back to main',buildMain);animated.push({object:ret,speed:.8});
    addLight(new THREE.HemisphereLight(0xf4e5b5,0x101010,.9));addLight(new THREE.PointLight(0xffd77c,1.6,50,2)).position.set(0,5,0);
    stats.textContent=`${room.title} · SVG floorplan · ${artCount} art anchors`;msg.textContent='SVG floorplan loaded from /app/maps/floorplan.svg.';
  }catch(e){
    reset();state.room='floorplan';setSpawn(room.spawn?.x??-24,room.spawn?.z??0,90);box(0,0,0,20,.1,20,mats.floor,false);box(-10,3,0,.4,6,20,mats.wall);box(10,3,0,.4,6,20,mats.wall);box(0,3,-10,20,6,.4,mats.wall);box(0,3,10,20,6,.4,mats.wall);stats.textContent=`${room.title} · fallback`;msg.textContent='SVG missing or unreadable; fallback room loaded.';
  }
}
JS;
$html = str_replace("function loadRoom(id){", $floorplanJs."\nfunction loadRoom(id){", $html);
$html = preg_replace("/else\s+msg\.textContent='No renderer for '\+r\.kind\}/","else if(r.kind==='floorplan-gallery'||r.kind==='floorplan gallery')buildFloorplan(r);else msg.textContent='No renderer for '+r.kind}",$html,1);

echo $html;
