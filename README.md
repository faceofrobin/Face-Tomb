# Face-Tomb

Face-Tomb is a browser-based Three.js exploration prototype built as a sequence of increasingly complex HTML builds (starter → mobile → walkthrough → impossible room), plus a reusable JSON layout definition.

## Repository layout

### Entry point and docs

- `README.md` — this project guide and change map.
- `index.php` — simple directory browser UI that lists files in this folder and links to each artifact directly.

### Core gameplay builds (HTML)

- `face_tomb_starter.html`
  - Baseline desktop-first Three.js tomb scene.
  - Includes map/minimap and the initial room progression logic.
- `face_tomb_starter_mobile.html`
  - Mobile-control branch of the starter experience.
  - Adds touch-target controls and responsive HUD behavior.
- `face_tomb_starter_mobile_v2.html`
  - Follow-up iteration of the mobile branch with additional UI/control tuning.
- `face_tomb_impossible_room.html`
  - Adds the non-Euclidean “impossible room” concept and secret-routing behavior.
- `face_tomb_walkthrough_ui_v2.html`
  - Adds/expands walkthrough-focused UI and messaging on top of the impossible-room branch.
- `face_tomb_walkthrough_v3 - CLAUDE.html`
  - Heavier integrated walkthrough build with expanded minimap/interaction complexity.
- `face_tomb_walkthrough_v_4_fixed.html`
  - Full-feature merged walkthrough build with layout-driven systems and impossible-room loop logic.
- `face_tomb_walkthrough_v5_return_portal.html`
  - Newest variant based on v4 fixed, with an explicit return portal flow that spawns the player back in the main hall.
  - Contains large embedded `layout-data` JSON, interactables, minimap, mobile controls, special spaces, and impossible-room loop logic.
- `face_tomb_walkthrough_v6_secret_spawn_recall.html`
  - Latest variant: keeps the main-hall return portal, adds a separate secret-room recall object that teleports to the original spawn point, and shifts secret-room visuals to a stronger blue palette.

### Lightweight v4 variant files

- `face_tomb_walkthrough_v4 - CHATGPT.html`
- `face_tomb_walkthrough_v4 - GEMINI.html`

Both are compact “professional v4” variants with:

- Inline minimal grid layout.
- HUD/crosshair/overlay setup.
- Movement, interaction raycast, dome restoration, and secret relic teleport behavior.

In this snapshot they are effectively identical in structure and size, suggesting parallel prompt/output variants for comparison.

### Data/layout artifacts

- `face_tomb_layout.json`
  - Canonical tomb layout model.
  - Defines grid rows, legend, room metadata, render defaults, materials, and suggested interactive spots.
- `face_tomb_layout_notes.txt`
  - Brief design note describing how the JSON should drive generated geometry (floors/walls/props).

## What changed across iterations

This repository appears to capture iterative design stages rather than a single app with folders. The progression is mostly encoded in filenames and embedded UI/logic content:

1. **Starter phase**
   - `face_tomb_starter.html` establishes the base scene and progression map.

2. **Mobile adaptation phase**
   - `face_tomb_starter_mobile.html` introduces touch/mobile controls.
   - `face_tomb_starter_mobile_v2.html` refines that mobile pass.

3. **Impossible-space gameplay phase**
   - `face_tomb_impossible_room.html` introduces the “four left turns” non-Euclidean mechanic.

4. **Guided walkthrough/UI polish phase**
   - `face_tomb_walkthrough_ui_v2.html` and `face_tomb_walkthrough_v3 - CLAUDE.html` expand instruction/UI presentation and integrated systems.

5. **v4 branch phase**
   - `face_tomb_walkthrough_v4 - CHATGPT.html` and `face_tomb_walkthrough_v4 - GEMINI.html` provide compact v4 implementations with explicit notes about fixed movement/speed and black-screen lighting restoration.

6. **Merged full walkthrough phase**
   - `face_tomb_walkthrough_v_4_fixed.html` consolidates major systems: layout-driven world construction, minimap, mobile controls, room markers, interactables, domed room, and impossible-room mechanics.

7. **Return-portal refinement phase**
   - `face_tomb_walkthrough_v5_return_portal.html` adds an explicit return-portal destination back to the main hall (pillared hall center).

8. **Secret-room recall + blue pass**
   - `face_tomb_walkthrough_v6_secret_spawn_recall.html` adds a distinct recall orb in the secret room that returns to the main spawn point, while re-theming the secret room to blue.

## Quick start

### Option A: Open files directly

Open any `*.html` file in a browser. For the most complete experience, start with:

- `face_tomb_walkthrough_v6_secret_spawn_recall.html`

### Option B: Use the local PHP index

From project root:

```bash
php -S 127.0.0.1:8000
```

Then open:

- `http://127.0.0.1:8000/index.php`

## Suggested cleanup next

- Move versions into folders (`/starter`, `/mobile`, `/walkthrough`, `/archive`).
- Extract shared JS/CSS from large HTML files into reusable modules.
- Choose one canonical runtime build (likely `face_tomb_walkthrough_v_4_fixed.html`) and mark others as historical snapshots.
- Add a changelog file with dated entries to replace filename-only version history.
