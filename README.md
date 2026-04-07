# Face-Tomb

Face Tomb now includes a browser GUI editor for the layout JSON.

## New GUI editor

- Open `face_tomb_layout_editor.html` in the same PHP-served directory as the rest of the project.
- It can:
  - paint the map grid,
  - adjust player start coordinates,
  - edit interactive/teleport-like object spots,
  - edit raw JSON directly,
  - download JSON,
  - save JSON back to `face_tomb_layout.json`.

## Save endpoint

- `save_layout.php` accepts `POST` JSON and writes `face_tomb_layout.json` with pretty formatting.
- If you host this publicly, add auth before exposing the save endpoint.
