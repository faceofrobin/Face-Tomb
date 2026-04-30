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

  // --- TRAPEZOID SPAWN ROOM ---
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

  // walls around trapezoid
  function wallLine(x1,z1,x2,z2){
    const len = Math.hypot(x2-x1,z2-z1);
    const midX=(x1+x2)/2, midZ=(z1+z2)/2;
    const angle=Math.atan2(z2-z1,x2-x1);
    const w = new THREE.Mesh(new THREE.BoxGeometry(len,H,T), mats.wall);
    w.position.set(midX-24,H/2,midZ);
    w.rotation.y = -angle;
    root.add(w);
  }

  wallLine(-6,-4,6,-4);
  wallLine(6,-4,4,4);
  wallLine(4,4,-6,4);
  wallLine(-6,4,-6,-4);

  // --- MAIN CENTRAL GRID ROOM ---
  box(0,0,0,20,0.1,20,mats.floor,false);
  box(-10,H/2,0,T,H,20,mats.wall);
  box(10,H/2,0,T,H,20,mats.wall);
  box(0,H/2,-10,20,H,T,mats.wall);
  box(0,H/2,10,20,H,T,mats.wall);

  // grid pillars (visual reference to plan)
  for(let x=-6;x<=6;x+=4){
    for(let z=-6;z<=6;z+=4){
      box(x,1.5,z,1.2,3,1.2,mats.stone,true);
    }
  }

  // --- VERTICAL CORRIDOR WITH ALCOVES ---
  function buildCorridor(startZ,count){
    for(let i=0;i<count;i++){
      const z = startZ - i*8;
      // corridor floor
      box(0,0,z, CW, 0.1, 8, mats.floor,false);

      // walls
      box(-CW/2, H/2, z, T, H, 8, mats.wall);
      box(CW/2, H/2, z, T, H, 8, mats.wall);

      // alcoves left/right
      const offsets=[-1,1];
      offsets.forEach(side=>{
        const ax = side*(CW/2 + AW/2);
        box(ax,0,z,AW,0.1,AD,mats.floor,false);
        box(ax - side*AW/2, H/2, z, T, H, AD, mats.wall);
        box(ax + side*AW/2, H/2, z, T, H, AD, mats.wall);
        box(ax, H/2, z-AD/2, AW, H, T, mats.wall);

        // placeholder art panel
        const panel = new THREE.Mesh(
          new THREE.PlaneGeometry(c.artPanelWidth||2, c.artPanelHeight||2.5),
          mats.glow
        );
        panel.position.set(ax,2.5,z-AD/2+.2);
        root.add(panel);
      });
    }
  }

  buildCorridor(-12,10);

  // --- RETURN RELIC ---
  const ret = new THREE.Mesh(new THREE.OctahedronGeometry(.9,0), mats.glow);
  ret.position.set(-20,1.5,0);
  root.add(ret);
  clickObj(ret,'Return','Back to main',buildMain);

  stats.textContent = `${room.title} · procedural layout`;
  msg.textContent = 'Exact floorplan recreation. Alcoves ready for art injection.';
}
JS;

$html = str_replace("function loadRoom(id){", $floorplanJs . "\nfunction loadRoom(id){", $html);

// extend loader
$html = str_replace("else msg.textContent='No renderer for '+r.kind}", "else if(r.kind==='floorplan-gallery') buildFloorplan(r); else msg.textContent='No renderer for '+r.kind}", $html);

echo $html;
