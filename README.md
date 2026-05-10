# Face-Tomb
circling something really strong here — not just a visual gimmick, but a mythology engine.


# Face Tomb  
*A continuously mutating Three.js mausoleum of portals, art, bars, glass towers, teleportation objects, wallpapers, hallway experiments, and aggressively preserved functionality.*

---

# README.md

## Overview

Welcome to **Face Tomb**, the increasingly elaborate digital monument to experimentation, procedural architecture, PHP-powered galleries, teleportation architecture, and “just one more feature.”

Face Tomb is not merely a 3D environment.

It is:

- a virtual exhibition hall
- a recursive art archive
- a testing ground for procedural architecture
- a room-based portal system
- a PHP-to-Three.js hybrid renderer
- a teleportation nexus
- a museum
- a bar
- a cathedral of image directories
- an argument against minimal scope creep
- and occasionally a functioning game engine.

The project combines:

- PHP scene generation
- Three.js rendering
- dynamically scanned image directories
- configurable rooms
- editor tooling
- teleport systems
- lazy loading
- procedural geometry
- mobile controls
- wall skinning
- spotlight systems
- SVG floorplan ambitions
- and a relentless determination to **not break the editor again.**

---

# Philosophy

The Face Tomb project follows several core principles:

## 1. Everything Is A Room

If something exists, it can probably become a room.

Examples:

- a hallway
- a bar
- an art gallery
- a glass tower chamber
- a conspiracy wall
- an endless image corridor
- a tarot archive
- a procedural SVG structure
- a room made entirely of teleports to other rooms

---

## 2. PHP Reads Directories

Almost every major feature begins with:

> “Can we make a PHP file that scans a directory…”

The system thrives on filesystem-driven content generation.

Directories are not storage.

Directories are worldbuilding.

---

## 3. Nothing Must Be Removed

Every iteration of Face Tomb is additive.

Or at least it is supposed to be.

Several development cycles involved:

- accidentally removing editor sliders
- deleting teleport objects
- destroying mobile controls
- breaking FOV handling
- erasing hallway functionality
- and creating mysterious empty yellow rooms

These events are now considered part of the lore.

---

## 4. Teleportation Should Be Weird

Traditional menus are weak.

Face Tomb prefers:

- benches
- pyramids
- abstract objects
- glowing structures
- impossible architecture
- symbolic room anchors

A room should not merely be selected.

It should be *encountered.*

---

# Core Architecture

## Stack

### Frontend

- Three.js
- PointerLockControls
- JavaScript
- HTML/CSS

### Backend

- PHP
- Directory scanning
- JSON configuration files

### Hosting

- HostGator deployment
- GitHub repository sync
- FTP deployment pipeline

### Project Repository

https://github.com/faceofrobin/Face-Tomb

---

# Major Systems

# 1. Main Chamber

The central hub of the Tomb.

Originally a simpler environment, the main chamber evolved into a configurable nexus connecting all experimental rooms.

Features include:

- circular room geometry
- configurable FOV
- teleportation objects
- wallpaper support
- dome projections
- movement configuration
- no-clip testing
- spawn rotation systems

The room acts as the “overworld” of the Face Tomb universe.

---

# 2. Teleportation Objects

Doors were eventually considered too ordinary.

Instead, rooms became accessible through:

- abstract sculptures
- benches
- geometric forms
- room-themed objects

The Art Room specifically uses a bench teleport object consisting of:

- two vertical supports
- one long connecting beam

The bench became strangely important.

---

# 3. The Bar Room

One of the earliest fully realized imported environments.

The BAR room includes:

- textured walls
- shelves
- bottles
- labels
- environmental atmosphere
- music playback
- teleport integration

Important development moments included:

- resizing shelves for 36 bottles
- fixing spawn collisions with the bar geometry
- increasing ceiling height
- ensuring autoplay music worked correctly

The BAR room represents the first moment Face Tomb became less of a test environment and more of an explorable world.

---

# 4. Glass Room

The Glass Room evolved into a towering image cathedral.

Features:

- circular stacked image rings
- preserved aspect ratios
- enormous vertical scale
- white chapel walls
- image tower systems

At one point:

- images failed to load
- only tan placeholders appeared
- rings clipped through the roof
- proportions became absurd

The final vision became:

> “Three massive towering image rings stacked vertically without touching the roof.”

A reasonable request.

---

# 5. Art History Room

Perhaps the most ambitious room structure so far.

The Art Room features:

- multi-room layouts
- narrow connecting hallways
- framed artworks
- benches
- spotlights
- wallpaper textures
- lazy loading systems

Architecture goals included:

- 6 artworks per room
- 3 artworks per side
- benches between art groups
- connected galleries

Performance optimizations later introduced:

- unloading distant art
- loading nearest artwork sets only
- reducing render overhead

Wallpaper source:

```plaintext
faceofrobin.com/AIFACES/ART/wallpaper.png
```

Artwork source:

```plaintext
faceofrobin.com/AIFACES/ART/
```

The room also marked the beginning of “museum logic” inside the Tomb.

---

# 6. Infinite Hallway

A procedural corridor environment derived from earlier PHP experiments.

Features:

- recursive hallway visuals
- infinite-feeling geometry
- teleport integration
- atmospheric traversal

The hallway helped establish the idea that:

> Rooms do not have to make architectural sense.

Which later became extremely important.

---

# 7. Dome Projection System

The main chamber dome eventually gained projected imagery.

Most notably:

- `fade.jpeg`

This feature required:

- curved dome UV mapping
- lowering image placement
- adjusting borders
- correcting visibility issues

Several versions resulted in:

- tiny white slivers
- floating gold borders
- almost-visible images
- existential uncertainty

Eventually the projection system stabilized.

Mostly.

---

# 8. Wallpaper Skinning System

Walls became dynamically skinnable.

Wallpaper support includes:

- tiled textures
- editor-based selection
- directory scanning
- dynamic material swapping

Wallpaper directory:

```plaintext
/tomb/images/wallpaper/
```

This allowed the Tomb to become visually mutable without code edits.

An extremely dangerous capability.

---

# Editor System

The Face Tomb editor became increasingly powerful after repeated user requests to:

> “Please do not remove the sliders this time.”

The editor eventually supported:

- spotlight brightness
- artwork size
- room height
- bench height
- FOV
- movement speed
- pan speed
- jump height
- spawn rotation
- wallpaper selection

The editor philosophy is:

- expose everything
- preserve everything
- never remove controls
- recover quickly from accidental destruction

---

# Movement System

Movement evolved substantially over time.

## Desktop Controls

- WASD movement
- PointerLock mouse look
- diagonal traversal
- configurable speed

## Mobile Controls

Eventually expanded to include:

- virtual joystick
- touch-safe interaction
- no accidental text selection
- teleport support

---

# No-Clip System

One of the most useful debugging tools.

Features:

- wall bypassing
- free camera movement
- visual toggle indicator
- testing geometry boundaries

Color states:

- Yellow = normal collision
- Blue = no-clip enabled

This system was critical for diagnosing:

- invisible walls
- teleport spawn errors
- improperly aligned geometry
- accidental giant towers

---

# Spawn System

Spawn logic became surprisingly complex.

Problems encountered:

- spawning inside bars
- facing walls
- incorrect rotation
- collision overlap

Solutions introduced:

- configurable spawn rotation
- spawn offsets
- safer placement logic
- editor controls

A 180° rotation became standard because users repeatedly spawned facing walls like confused archaeologists.

---

# SVG Floorplan System

One of the most experimental systems.

Goals:

- import SVG layouts
- convert paths into geometry
- support arbitrary floorplans
- enable room generation from diagrams

Early experiments included:

- trapezoidal rooms
- looping corridors
- procedural hall systems

Eventually the ambition became:

> “Change the SVG and the Tomb changes.”

Which is either brilliant or catastrophic.

Possibly both.

---

# Lazy Loading System

As the number of artworks increased, performance began to collapse under the weight of beauty.

Solutions included:

- proximity-based loading
- nearest-art prioritization
- unloading distant assets
- dynamic visibility management

Target behavior:

- load nearest ~12 artworks
- unload distant art objects

This dramatically improved responsiveness.

And prevented the browser from quietly dying.

---

# Design Language

Face Tomb aesthetics include:

- black and gold
- surreal geometry
- art deco influences
- gallery minimalism
- impossible architecture
- recursive symbolism
- mysterious portals
- dramatic spotlights
- floating objects
- oversized imagery

The environment exists somewhere between:

- museum
- dream
- nightclub
- shrine
- software test harness
- haunted operating system

---

# Image Directory Philosophy

A core Face Tomb principle:

> “The filesystem is the database.”

Most environments scan directories directly.

Examples:

```plaintext
/AIFACES/ART/
/AIFACES/BAR/
/AIFACES/GLASS/
/AIFACES/HALLWAY/
/AIFACES/TAROT/
/AIFACES/COMIC/
```

The Tomb treats media folders as living content systems.

Adding files changes worlds.

---

# Development Lessons Learned

## 1. FOV Changes Can Destroy Reality

Changing FOV once accidentally removed:

- sliders
- teleports
- editor UI
- functionality

This event is remembered.

---

## 2. Performance Matters

Loading hundreds of textures simultaneously is exciting until:

- frame rates collapse
- mobile devices overheat
- browsers become philosophical

Lazy loading became mandatory.

---

## 3. Additive Development Is Hard

The project repeatedly emphasized:

> “Do not remove anything.”

This turns every update into digital archaeology.

---

## 4. PHP Is Weirdly Good At This

The combination of:

- PHP filesystem scanning
- JSON configs
- Three.js rendering

created an unexpectedly powerful content pipeline.

---

# Planned Features

Potential future directions include:

- procedural audio zones
- multiplayer exploration
- NPCs
- AI-guided tours
- room scripting
- dynamic weather
- volumetric fog
- floorplan editors
- visual node-based room creation
- animated teleport objects
- recursive room generation
- live collaborative editing
- portal recursion
- floating geometry
- dream logic transitions

And probably several things that were absolutely not planned originally.

---

# Folder Structure

```plaintext
/tomb/
│
├── /app/
│   ├── viewer
│   ├── controls
│   ├── teleport systems
│   └── room renderers
│
├── /api/
│   └── scene.php
│
├── /editor/
│   ├── sliders
│   ├── config tools
│   └── wallpaper selectors
│
├── /images/
│   ├── wallpaper/
│   └── textures/
│
├── /maps/
│   └── floorplan.svg
│
└── /AIFACES/
    ├── ART/
    ├── BAR/
    ├── GLASS/
    ├── HALLWAY/
    ├── TAROT/
    └── COMIC/
```

---

# Final Notes

Face Tomb is ultimately a project about:

- experimentation
- persistence
- procedural art
- modular worlds
- evolving systems
- recursive aesthetics
- and stubborn creative escalation

It is simultaneously:

- a software project
- a digital museum
- an art engine
- a memory palace
- and a very elaborate excuse to keep adding one more room.

If you are reading this:

welcome to the Tomb.

Please avoid changing the FOV without backing things up first.
