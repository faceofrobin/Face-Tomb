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

// --- FLOORPLAN ROOM INJECTION ---
$floorplanJs = <<<'JS'
function buildFloorplan(room){
  reset();
  state.room='floorplan';

  const c = room.config||{};
  const H = c.wallHeight||6;
  const T = c.wallThickness||0.4;
  const CW = c.corridorWidth||4;
  const AW = c.alcoveWidth||4.2;
  const AD = c.alcoveDepth||4.8;

  scene.background = new THREE.Color(0x060606);
  scene.fog = new THREE.FogExp2(0x060606, .012);

  setSpawn(room.spawn?.x??-24, room.spawn?.z??0, 90);

  const trap = new THREE.Shape();
  trap.moveTo(-6,-4);
  trap.lineTo(6,-4);
  trap.lineTo(4,4);
  trap.lineTo(-6,4);
  trap.closePath();

  const floorGeo = new THREE.ShapeGeometry(trap);
  const floor = new THREE.Mesh(floorGeo, mats.floor);
  floor.rotation.x = -Math.PI/2;
  floor.position.set(-24,0,0);
  root.add(floor);

  function wallLine(x1,z1,x2,z2){
    const len = Math.hypot(x2-x1,z2-z1);
    const midX=(x1+x2)/2, midZ=(z1+z2)/2;
    const angle=Math.atan2(z2-z1,x2-x1);
    const w = new THREE.Mesh(new THREE.BoxGeometry(len,H,T), mats.wall);
    w.position.set(midX-24,H/2,midZ);
    w.rotation.y = -angle;
    root.add(w);
    colliders.push({x:midX-24,z:midZ,w:Math.max(len,T),d:Math.max(len,T)});
  }

  wallLine(-6,-4,6,-4);
  wallLine(6,-4,4,4);
  wallLine(4,4,-6,4);
  wallLine(-6,4,-6,-4);

  box(0,0,0,20,0.1,20,mats.floor,false);
  box(-10,H/2,0,T,H,20,mats.wall);
  box(10,H/2,0,T,H,20,mats.wall);
  box(0,H/2,-10,20,H,T,mats.wall);
  box(0,H/2,10,20,H,T,mats.wall);

  // temporary connector so the guessed procedural version is no longer separated
  box(-15,0,0,10,0.1,4,mats.floor,false);
  box(-15,H/2,-2,10,H,T,mats.wall);
  box(-15,H/2,2,10,H,T,mats.wall);

  for(let x=-6;x<=6;x+=4){
    for(let z=-6;z<=6;z+=4){
      box(x,1.5,z,1.2,3,1.2,mats.stone,true);
    }
  }

  function buildCorridor(startZ,count){
    for(let i=0;i<count;i++){
      const z = startZ - i*8;
      box(0,0,z, CW, 0.1, 8, mats.floor,false);
      box(-CW/2, H/2, z, T, H, 8, mats.wall);
      box(CW/2, H/2, z, T, H, 8, mats.wall);

      [-1,1].forEach(side=>{
        const ax = side*(CW/2 + AW/2);
        box(ax,0,z,AW,0.1,AD,mats.floor,false);
        box(ax + side*AW/2, H/2, z, T, H, AD, mats.wall);
        box(ax, H/2, z-AD/2, AW, H, T, mats.wall);

        const panel = new THREE.Mesh(new THREE.PlaneGeometry(c.artPanelWidth||2, c.artPanelHeight||2.5), mats.glow);
        panel.position.set(ax,2.5,z-AD/2+.22);
        root.add(panel);
      });
    }
  }

  buildCorridor(-12,10);

  const ret = new THREE.Mesh(new THREE.OctahedronGeometry(.9,0), mats.glow);
  ret.position.set(-20,1.5,0);
  root.add(ret);
  clickObj(ret,'Return','Back to main',buildMain);
  animated.push({object:ret,speed:.8});

  stats.textContent = `${room.title} · floorplan renderer`;
  msg.textContent = 'Floorplan loaded. SVG importer is next; dome Face is restored.';
}
JS;

$html = str_replace("function loadRoom(id){", $floorplanJs . "\nfunction loadRoom(id){", $html);

$html = preg_replace(
  "/else\s+msg\.textContent='No renderer for '\+r\.kind\}/",
  "else if(r.kind==='floorplan-gallery'||r.kind==='floorplan gallery')buildFloorplan(r);else msg.textContent='No renderer for '+r.kind}",
  $html,
  1
);

echo $html;
