---
name: deploy
description: >-
  Workflow to deploy updates of Agenda de Acuerdos (agenda-acuerdos-lideres)
  to Debian GNU/Linux WEB01 (192.168.100.14) using deterministic release artifacts.
---

# Deploy Workflow: Debian GNU/Linux (`WEB01` — `192.168.100.14`)

Esta skill documenta el procedimiento oficial para actualizar `D:\Sistemas\repos\agenda-acuerdos-lideres` desde GitHub y desplegar actualizaciones de **Agenda de Acuerdos** en **Debian GNU/Linux (`WEB01` — `192.168.100.14`)**.

*(Nota: El Servidor 12 `192.168.100.12` fue dado de baja conforme al procedimiento `baja-sistema-servidor12`; no se deben recrear carpetas productivas en `D:\Sistemas\apps\agenda-acuerdos-lideres` ni `C:\inetpub\wwwroot\agenda-acuerdos-lideres`.)*

---

## Arquitectura en Producción (`WEB01`)

* **Repositorio Git Local**: `D:\Sistemas\repos\agenda-acuerdos-lideres`
* **Servidor Web (`WEB01`)**: `mapetzinweb@192.168.100.14`
* **Symlink Activo**: `/var/www/apps/agenda` -> `/var/www/agenda-releases/<sha256>`
* **Usuario Linux Runtime / PHP-FPM**: `agenda` (`/run/php/php8.4-fpm-agenda.sock`)
* **Configuración Privada**: `/etc/agenda/runtime.env` (`SIAMT_SSO_ENABLED=true`, `KPI_DB_CONNECTION=mysql`, `KPI_UNIFY_TI_WITH_TICKETS=false`)
* **Storage Persistente**: `/var/lib/agenda/storage`
* **Base de Datos (`DB01` — `192.168.100.13`)**: `agenda_acuerdos` (usuario `user_agenda01@192.168.100.14`) y SSO central `auth_center` (`mysql_auth`)
* **URL Pública SIAMT**: `https://agenda-siamt.mt.local`

---

## Procedimiento de Despliegue Homologado

### 1. Actualizar el Repositorio Local desde GitHub
```powershell
Set-Location D:\Sistemas\repos\agenda-acuerdos-lideres
git fetch origin --prune
git pull origin main
```

### 2. Ejecutar el Orquestador Oficial (`deploy.ps1`)
```powershell
Set-Location D:\Sistemas\repos\agenda-acuerdos-lideres
powershell -ExecutionPolicy Bypass -File .\deploy.ps1 -SaveVault
```

El script ejecuta:
1. Instala dependencias Node (`npm ci`) si hace falta y compila los assets Vite (`npm run build`).
2. Valida sintaxis PHP de componentes críticos (`php -l`).
3. Empaqueta un artefacto tar.gz determinista (`ops/build-agenda-artifact.py`) desde el commit Git fijo e incluye `public/build` compilado y `.release/source-manifest.json`, excluyendo `.env`, `vendor`, `storage` y archivos heredados de depuración (`public/debug_url.php`, `public/test.php`).
4. Transfiere el artefacto y scripts por SSH/SFTP (`ops/ssh-web01.py`) a `/var/tmp/` y ejecuta `ops/publish-agenda-artifact.sh` bajo `sudo` con rollback automático ante cualquier fallo.
5. Regenera `package:discover`, `config:cache`, `route:cache`, `view:cache` como usuario `agenda`, ejecuta `ops/verify-agenda-release.php`, cambia atómicamente `/var/www/apps/agenda`, recarga `php8.4-fpm` y comprueba `https://agenda-siamt.mt.local/login` (HTTP `302`).

---

## Reglas de Higiene y Aislamiento de Entorno

1. **SIAMT SSO en Producción vs Local**: La integración SSO (`app/AuthCenter/Satellite/*`, `app/Siamt/*`, `config/siamt-sso.php`, `routes/siamt-sso.php`) se activa únicamente cuando `SIAMT_SSO_ENABLED=true` (`/etc/agenda/runtime.env`). En desarrollo local (`SIAMT_SSO_ENABLED=false`) permanece activo el login tradicional.
2. **KPIs Separados**: Mantener `KPI_DB_CONNECTION=mysql` y `KPI_UNIFY_TI_WITH_TICKETS=false` en `config/database.php` y `app/Livewire/KpisAreas.php`.
3. **Sin `.env` en Git**: El `.env` productivo vive exclusivamente en `/etc/agenda/runtime.env`.
