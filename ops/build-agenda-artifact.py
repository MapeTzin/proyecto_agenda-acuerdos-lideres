"""Build an immutable Agenda de Acuerdos artifact from a fixed Git commit and current Vite assets.

Does not create database copies or include private configuration, vendor or storage.
"""
import argparse
import gzip
import hashlib
import io
import json
from pathlib import Path, PurePosixPath
import subprocess
import tarfile

EXCLUDED_EXACT = {
    'public/hot',
    'public/storage',
    'public/debug_url.php',
    'public/test.php',
    'public/check_all_dbs.php',
    'public/check_dbs.php',
}

root = Path(__file__).resolve().parent.parent
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('--commit', required=True)
parser.add_argument('--output', type=Path, required=True)
parser.add_argument(
    '--exclude-uncommitted-runtime',
    action='store_true',
    help='Explicitly build Git runtime sources while preserving unrelated local edits',
)
args = parser.parse_args()

commit = subprocess.check_output(['git', 'rev-parse', args.commit + '^{commit}'], cwd=root, text=True).strip()
timestamp = int(subprocess.check_output(['git', 'show', '-s', '--format=%ct', commit], cwd=root, text=True))

candidate_paths = [
    'app',
    'bootstrap',
    'config',
    'database/migrations',
    'lang',
    'public',
    'resources',
    'routes',
    'artisan',
    'composer.json',
    'composer.lock',
]
paths = [p for p in candidate_paths if (root / p).exists()]

dirty_runtime = subprocess.check_output(['git', 'diff', '--name-only', commit, '--', *paths], cwd=root, text=True).splitlines()
if dirty_runtime and not args.exclude_uncommitted_runtime:
    parser.error('Runtime sources differ from the fixed commit; review or explicitly exclude local edits')

asset_inputs = [p for p in ('resources', 'public', 'package.json', 'package-lock.json', 'postcss.config.js', 'vite.config.js') if (root / p).exists()]
if subprocess.run(['git', 'diff', '--quiet', commit, '--', *asset_inputs], cwd=root).returncode:
    parser.error('Asset build inputs differ from the fixed commit')

output = args.output.resolve()
if output.exists():
    parser.error('Refusing to overwrite an artifact: ' + str(output))

build = root / 'public/build'
manifest_path = build / 'manifest.json'
if not manifest_path.is_file():
    parser.error('Run npm run build first')

build_manifest = json.loads(manifest_path.read_text(encoding='utf-8'))
for entry in build_manifest.values():
    for name in [entry.get('file'), *entry.get('css', [])]:
        if name and (not (build / name).resolve().is_relative_to(build.resolve()) or not (build / name).is_file()):
            parser.error('Missing or unsafe Vite asset: ' + str(name))

files = {}
process = subprocess.Popen(
    ['git', '-c', 'core.autocrlf=false', 'archive', '--format=tar', commit, '--', *paths],
    cwd=root,
    stdout=subprocess.PIPE,
)
with tarfile.open(fileobj=process.stdout, mode='r|') as archive:
    for member in archive:
        if member.isdir():
            continue
        name = PurePosixPath(member.name)
        if not member.isfile() or name.is_absolute() or '..' in name.parts:
            raise SystemExit('Unexpected Git archive member: ' + member.name)
        if name.as_posix() in EXCLUDED_EXACT:
            continue
        if any(part.startswith('.env') for part in name.parts) or name.parts[:2] == ('public', 'storage'):
            raise SystemExit('Private/runtime path in Git archive: ' + name.as_posix())
        if name.parts[0] == 'public' and name.suffix == '.php' and name.as_posix() != 'public/index.php':
            raise SystemExit(f'Unexpected PHP file inside public/: {name.as_posix()}')
        if name.parts[:2] == ('bootstrap', 'cache'):
            continue
        files[name.as_posix()] = archive.extractfile(member).read()

if process.wait() != 0:
    raise SystemExit('Git archive failed')

for path in sorted(build.rglob('*')):
    if path.is_symlink():
        raise SystemExit('Symlink in build assets')
    if path.is_file():
        files[path.relative_to(root).as_posix()] = path.read_bytes()

receipt = {
    'application': 'agenda',
    'source_commit': commit,
    'source_timestamp': timestamp,
    'excluded_local_runtime_edits': dirty_runtime,
    'files': {name: hashlib.sha256(data).hexdigest() for name, data in sorted(files.items())},
}
files['.release/source-manifest.json'] = (json.dumps(receipt, indent=2) + '\n').encode('utf-8')

output.parent.mkdir(parents=True, exist_ok=True)
with output.open('xb') as stream:
    with gzip.GzipFile(filename='', mode='wb', fileobj=stream, mtime=timestamp) as compressed:
        with tarfile.open(fileobj=compressed, mode='w') as archive:
            for name, data in sorted(files.items()):
                member = tarfile.TarInfo(name)
                member.size, member.mtime, member.mode = len(data), timestamp, 0o644
                archive.addfile(member, io.BytesIO(data))

digest = hashlib.sha256(output.read_bytes()).hexdigest()
output.with_suffix('.sha256').write_text(digest + '\n', encoding='ascii', newline='\n')
print(
    json.dumps(
        {
            'artifact': str(output),
            'source_commit': commit,
            'sha256': digest,
            'files': len(files),
            'bytes': output.stat().st_size,
        }
    )
)
