<?php
ob_start();
require __DIR__ . '/source-rooms.php';
$html = ob_get_clean();

// Version 4: The Archive Breathes.
$html = str_replace(
  '<div id="hud"><h1>The Face Tomb</h1>',
  '<div id="hud"><h1>The Face Tomb</h1><div style="margin:2px 0 7px;color:#e7d29a;font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase">Version 4 · The Archive Breathes</div>',
  $html
);

// Three examples modules use bare `three` imports unless unpkg rewrites them in module mode.
$html = str_replace(
  'https://unpkg.com/three@0.161.0/examples/jsm/loaders/GLTFLoader.js',
  'https://unpkg.com/three@0.161.0/examples/jsm/loaders/GLTFLoader.js?module',
  $html
);
$html = str_replace(
  'https://unpkg.com/three@0.161.0/examples/jsm/loaders/SVGLoader.js',
  'https://unpkg.com/three@0.161.0/examples/jsm/loaders/SVGLoader.js?module',
  $html
);

// Repair the generated Observatory callback chain. The source adapter was missing one closing parenthesis.
$html = str_replace(
  'depthWrite:false}))));const d=fibonacciDir',
  'depthWrite:false})))));const d=fibonacciDir',
  $html
);

// Run room-specific animation callbacks globally, not only after Art History has been visited.
$oldAnimate = "animated.forEach(a=>a.object.rotation.y+=a.speed*dt);move(dt);renderer.render(scene,camera)";
$newAnimate = "animated.forEach(a=>{if(a.object?.rotation)a.object.rotation.y+=(a.speed||0)*dt;if(typeof a.tick==='function')a.tick(dt)});move(dt);renderer.render(scene,camera)";
$html = str_replace($oldAnimate, $newAnimate, $html);

// The altar should be approachable. Keep a small central collision core instead of blocking the entire dais.
$html = str_replace(
  "cyl(0,.24,0,9.4,.48,mats.stone,true,altar);",
  "cyl(0,.24,0,9.4,.48,mats.stone,false,altar);colliders.push({x:0,z:0,w:2.2,d:2.2});",
  $html
);

echo $html;
