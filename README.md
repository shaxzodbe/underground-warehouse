# Underground Warehouse Management System

A full-stack application for managing an underground warehouse with a manipulator.

## Stack
- **Backend**: PHP 8.4 (Yii2), MariaDB, Redis, Supervisor
- **Frontend**: React (Vite), TypeScript, MUI, Redux Toolkit
- **Infrastructure**: Docker Compose

## Features
- **Manipulator Control**: Move (NASD/Up-Down-Left-Right), Pick, Drop.
- **Samples**: Normal and Cooling types.
- **Cooling Logic**: Cooling samples expire if kept outside the "Fridge Zone" (3x3 area at bottom-left) for > 30 seconds.
- **Visualization**: Real-time grid view.
- **Compression**: Command compression logic (RLE).

## Setup & Run

1. **Clone the repository.**
2. **Start Docker Containers:**
   ```bash
   docker compose up -d --build
   ```
3. **Initialize the Backend:**
   Applying migrations and starting the expiration monitoring job is required.
   ```bash
   # Apply Migrations
   docker compose exec backend php yii migrate --interactive=0
   
   # Start Monitoring Job
   docker compose exec backend php yii job/start
   ```
4. **Access the Application:**
   - **Frontend**: [http://localhost:3883](http://localhost:3883)
   - **Backend API**: [http://localhost:3884](http://localhost:3884)

## Architecture
- **ManipulatorService**: Handles movement logic, expiry updates, and history logging. Uses Redis for state.
- **CheckExpirationJob**: A recursive queue job that checks for expired cooling samples every 5 seconds.
- **Frontend**: Polls API for state updates every 1-2 seconds.

## Usage
- **Login**: Enter any username/password.
- **Control Panel**: Enter commands like `5P3B`. Note: Implementation uses char-by-char execution.
- **Commands**:
  - `В` / `U`: Up (Y+)
  - `Н` / `D`: Down (Y-)
  - `Л` / `L`: Left (X-)
  - `П` / `R`: Right (X+)
  - `О` / `P`: Pick (Pick / Grab)
  - `Б` / `E`: Drop (Brosit / Eject)
- **Fridge Zone**: Shown in Blue (Bottom Left 3x3).

## Notes
- Database runs on host port 38306 to avoid conflicts.
- Frontend Auth is mocked for demonstration.
