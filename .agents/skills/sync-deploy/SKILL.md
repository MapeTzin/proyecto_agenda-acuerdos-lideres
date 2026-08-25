---
name: sync-deploy
description: >-
  Workflow to synchronize local application modifications from production (app)
  to repository (repo) and push changes to GitHub.
---

# Sync-Deploy: Reverse Synchronization & Git Push Workflow

Use this skill when changes are made directly to the production application folder (`D:\Sistemas\apps\agenda-acuerdos-lideres`) instead of the development repository (`D:\Sistemas\repos\agenda-acuerdos-lideres`). This runbook ensures that the local git repository is kept up to date and that changes are pushed to GitHub without carrying over temporary runtime residues (such as cache, vendor libraries, node modules, or database configurations).

---

## 🛠️ Requirements & Setup

A script is pre-installed on this server to automate the file-copying logic and Git synchronization:
* **Script Location**: `D:\Sistemas\scripts\sync_app_to_repo.ps1`
* **Purpose**: Mirrors changes from `apps/agenda-acuerdos-lideres` back to `repos/agenda-acuerdos-lideres` for standard folders (`app/`, `config/`, `database/`, `resources/`, `routes/`) and root configurations, then commits and pushes them to GitHub.

---

## 📋 Synchronization & Git Push Procedure

To synchronize changes and push them to GitHub, follow these steps in order:

### Step 1: Execute Reverse Synchronization
Open a PowerShell console (with administrator privileges if required) and run the sync script:
```powershell
& "D:\Sistemas\scripts\sync_app_to_repo.ps1"
```

### Step 2: Verify Sourced Folders
Confirm that only source files have been copied to the repository by running:
```powershell
git -C D:\Sistemas\repos\agenda-acuerdos-lideres status
```
* **Verify**: Ensure that folders like `node_modules`, `vendor`, or `.env` files are **not** present in the staging area or marked as modified.

### Step 3: Verify Remote Synchronization
Confirm that the commit has been successfully pushed by looking at the git push log in the console or running:
```powershell
git -C D:\Sistemas\repos\agenda-acuerdos-lideres log -n 5 --oneline
```
The latest commit should have the message format:
`Sincronizacion automatica de cambios directos en servidor - YYYY-MM-DD HH:mm:ss`
