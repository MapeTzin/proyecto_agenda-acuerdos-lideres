"""Run an explicit deployment command on WEB01 (192.168.100.14); credentials arrive only on stdin."""
import json
from pathlib import Path, PurePosixPath
import shlex
import sys
import paramiko

request = json.load(sys.stdin)
if request.get('host') != '192.168.100.14' or request.get('user') != 'mapetzinweb':
    raise SystemExit('Host/user outside authorized WEB01 scope')

client = paramiko.SSHClient()
client.load_system_host_keys()
known_hosts = Path.home() / '.ssh' / 'known_hosts'
if known_hosts.is_file():
    client.load_host_keys(str(known_hosts))
client.set_missing_host_key_policy(paramiko.RejectPolicy())

try:
    default_key = Path.home() / '.ssh' / 'id_ed25519'
    key = request.get('key_filename') or (str(default_key) if default_key.is_file() else None)
    client.connect(
        request['host'],
        username=request['user'],
        password=None,
        key_filename=key,
        look_for_keys=False,
        allow_agent=False,
        timeout=15,
    )
    if request.get('uploads'):
        with client.open_sftp() as sftp:
            for upload in request['uploads']:
                local = Path(upload['local'])
                remote = PurePosixPath(upload['remote'])
                if remote.parent != PurePosixPath('/var/tmp'):
                    raise ValueError('Uploads limited to WEB01 /var/tmp staging directory')
                if not local.is_file() or local.is_symlink():
                    raise ValueError('Regular local upload required: ' + str(local))
                if not upload.get('overwrite', False):
                    try:
                        sftp.lstat(str(remote))
                    except FileNotFoundError:
                        pass
                    else:
                        raise ValueError('Refusing to overwrite staged upload: ' + str(remote))
                sftp.put(str(local), str(remote))
                sftp.chmod(str(remote), 0o600)

    command = request['command']
    if request.get('sudo'):
        if not request.get('password'):
            raise ValueError('Sudo password required on stdin payload')
        command = 'sudo -S -p "" -- bash -c ' + shlex.quote(command)

    stdin, stdout, stderr = client.exec_command(command, timeout=180)
    if request.get('sudo'):
        stdin.write(request['password'] + '\n')
        stdin.flush()
    stdin.channel.shutdown_write()

    result = {
        'host': request['host'],
        'exit_code': stdout.channel.recv_exit_status(),
        'stdout': stdout.read().decode('utf-8', 'replace'),
        'stderr': stderr.read().decode('utf-8', 'replace'),
    }
    print(json.dumps(result, ensure_ascii=True))
    raise SystemExit(0 if result['exit_code'] == 0 else result['exit_code'])
finally:
    client.close()
