<?php
$html = file_get_contents(__DIR__ . '/index.html');

// --- NEW FLOORPLAN ROOM INJECTION ---
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

        const panel = new THREE.Mesh(
          new THREE.PlaneGeometry(c.artPanelWidth||2, c.artPanelHeight||2.5),
          mats.glow
        );
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

  stats.textContent = `${room.title} · procedural floorplan · alcoves ready`;
  msg.textContent = 'Exact floorplan recreation loaded.';
}
JS;

$html = str_replace("function loadRoom(id){", $floorplanJs . "\nfunction loadRoom(id){", $html);

// Robustly route both floorplan-gallery and floorplan gallery to the renderer.
$html = preg_replace(
  "/else\s+msg\.textContent='No renderer for '\+r\.kind\}/",
  "else if(r.kind==='floorplan-gallery'||r.kind==='floorplan gallery')buildFloorplan(r);else msg.textContent='No renderer for '+r.kind}",
  $html,
  1
);

echo $html;
