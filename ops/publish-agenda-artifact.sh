#!/bin/bash
set -Eeuo pipefail
umask 027

[[ "$(hostname)" == WEB01 ]]
archive=${1:?archive required}
expected=${2:?artifact SHA256 required}
commit=${3:?source commit required}
verification=${4:?verification script required}

[[ "$expected" =~ ^[a-f0-9]{64}$ && "$commit" =~ ^[a-f0-9]{40}$ ]]
[[ "$archive" == "/var/tmp/agenda-artifact-$expected.tar.gz" ]]
[[ "$verification" == /var/tmp/agenda-verify-*.php ]]
[[ "$(sha256sum "$archive" | cut -d' ' -f1)" == "$expected" ]]

directory="/var/www/agenda-releases/$expected"
previous=$(readlink -f /var/www/apps/agenda)
[[ "$previous" == /var/www/agenda-releases/* ]]
test -f "$verification"
env_before=$(sha256sum /etc/agenda/runtime.env | cut -d' ' -f1)
activated=0

rollback() {
    set +e
    if [[ "$activated" == 1 ]]; then
        ln -sfn "$previous" /var/www/apps/agenda.pending
        mv -Tf /var/www/apps/agenda.pending /var/www/apps/agenda
        systemctl reload php8.4-fpm
        echo 'Agenda activation failed; previous symlink restored.' >&2
    else
        rm -rf "$directory" 2>/dev/null || true
        echo 'Agenda preparation failed; active symlink unchanged.' >&2
    fi
    exit 1
}
trap rollback ERR

# Validate the entire tar before extraction. Nothing from the archive may be a link.
python3 - "$archive" "$commit" <<'PY'
import json, sys, tarfile
from pathlib import PurePosixPath

with tarfile.open(sys.argv[1], 'r:gz') as archive:
    seen = set()
    for member in archive:
        path = PurePosixPath(member.name)
        if path.is_absolute() or '..' in path.parts or not member.isfile():
            raise SystemExit('Unsafe artifact member: ' + member.name)
        if member.name in seen or any(p.startswith('.env') for p in path.parts):
            raise SystemExit('Duplicate/private artifact member: ' + member.name)
        if path.parts[0] in {'storage', 'vendor', 'node_modules', '.git'}:
            raise SystemExit('Unexpected runtime/private directory: ' + member.name)
        if path.parts[0] == 'public' and path.suffix == '.php' and path.as_posix() != 'public/index.php':
            raise SystemExit('Unexpected PHP file inside public/: ' + member.name)
        seen.add(member.name)
    manifest = json.load(archive.extractfile('.release/source-manifest.json'))
    if manifest.get('application') != 'agenda':
        raise SystemExit('Artifact application mismatch')
    if manifest['source_commit'] != sys.argv[2]:
        raise SystemExit('Artifact source commit mismatch')
    if set(manifest['files']) | {'.release/source-manifest.json'} != seen:
        raise SystemExit('Artifact manifest/member mismatch')
    if 'public/build/manifest.json' not in seen:
        raise SystemExit('Artifact missing compiled Vite manifest')
PY

if [[ ! -e "$directory" ]]; then
    install -d -m 751 -o root -g agenda "$directory"
    tar -xzf "$archive" --no-same-owner -C "$directory"
    cd "$directory"
    COMPOSER_ALLOW_SUPERUSER=1 composer install --quiet --no-dev --no-scripts --no-plugins --prefer-dist --no-interaction --optimize-autoloader
    chown -R root:agenda "$directory"
    find "$directory" -type d -exec chmod 750 {} +
    find "$directory" -type f -exec chmod 640 {} +
    chmod 751 "$directory"
    find "$directory/public" -type d -exec chmod 755 {} +
    find "$directory/public" -type f -exec chmod 644 {} +
    install -d -o agenda -g agenda -m 750 "$directory/bootstrap/cache"
    ln -s /var/lib/agenda/storage "$directory/storage"
    ln -s /etc/agenda/runtime.env "$directory/.env"
    ln -s /var/lib/agenda/storage/app/public "$directory/public/storage"
fi

install -m 640 -o root -g agenda "$verification" "$directory/.release/verify-agenda-release.php"
cd "$directory"
sudo -u agenda php artisan package:discover --quiet
sudo -u agenda php artisan config:cache --quiet
sudo -u agenda php artisan route:cache --quiet
sudo -u agenda php artisan view:cache --quiet
sudo -u agenda php "$directory/.release/verify-agenda-release.php" "$directory" "$commit" >/dev/null
test "$(sha256sum /etc/agenda/runtime.env | cut -d' ' -f1)" = "$env_before"

ln -sfn "$directory" /var/www/apps/agenda.pending
mv -Tf /var/www/apps/agenda.pending /var/www/apps/agenda
activated=1
systemctl reload php8.4-fpm

status=$(curl --silent --show-error --cacert /etc/authcenter-public/hub-web-ca.pem \
    --resolve agenda-siamt.mt.local:443:127.0.0.1 --output /dev/null --write-out '%{http_code}' https://agenda-siamt.mt.local/login)
[[ "$status" == 302 ]]

sudo -u agenda php "$directory/.release/verify-agenda-release.php" /var/www/apps/agenda "$commit"
printf '%s\n' "$expected" > /etc/agenda/installed-artifact
trap - ERR
printf 'Agenda published: commit=%s artifact=%s HTTP=%s\n' "$commit" "$expected" "$status"
