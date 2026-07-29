@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Agenda de Acuerdos')
@section('header_title', 'Gestión de Usuarios')

@section('content')
<div class="card" style="background: white; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="color: #1e293b; font-size: 1.25rem; font-weight: 700;">Gestión de Usuarios</h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.1rem;">Listado de usuarios con acceso al sistema</p>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button class="btn btn-primary" onclick="openAddMemberModal()" style="padding: 0.6rem 1.2rem; border-radius: 0.5rem; background: #6366f1;">
                <i class="fas fa-user-plus"></i> Agregar Usuario
            </button>
        </div>
    </div>
    </div>

    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <th style="padding: 1rem; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-align: left;">Usuario</th>
                    <th style="padding: 1rem; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-align: left;">Email</th>
                    <th style="padding: 1rem; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-align: left;">Área / Departamento</th>
                    <th style="padding: 1rem; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-align: center;">Color</th>
                    <th style="padding: 1rem; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-align: left;">Rol</th>
                    <th style="padding: 1rem; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody style="color: #1e293b;">
                @foreach($users as $user)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; background: {{ $user->area_color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">{{ $user->name }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">{{ $user->position }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem; font-size: 0.875rem; color: #475569;">{{ $user->email }}</td>
                    <td style="padding: 1rem;">
                        <span style="font-size: 0.75rem; font-weight: 600; color: #475569; background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 0.375rem;">
                            {{ implode(' y ', $user->areas) }}
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        <div style="width: 20px; height: 20px; background: {{ $user->area_color }}; border-radius: 50%; margin: 0 auto; border: 2px solid white; box-shadow: 0 0 0 1px #e2e8f0;"></div>
                    </td>
                    <td style="padding: 1rem;">
                        @foreach($user->roles as $role)
                        <span style="font-size: 0.7rem; font-weight: 700; color: #4f46e5; background: #eef2ff; padding: 0.2rem 0.5rem; border-radius: 0.25rem;">
                            {{ strtoupper($role->name) }}
                        </span>
                        @endforeach
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button onclick="editUser({{ $user->toJson() }}, '{{ $user->roles->first()?->name }}')" class="btn" title="Editar" style="padding: 0.4rem; color: #6366f1; background: #f5f3ff;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="toggleStatus({{ $user->id }})" class="btn" title="Quitar Acceso" style="padding: 0.4rem; color: #f59e0b; background: #fffbeb;">
                                <i class="fas fa-user-slash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="padding: 1rem 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
        {{ $users->links() }}
    </div>
</div>

<!-- Modal para Agregar Usuario Existente -->
<div id="addMemberModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 1rem; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
        <h3 style="color: #1e293b; font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Dar Acceso a Usuario</h3>
        <form id="addMemberForm">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="color: #64748b; font-size: 0.8rem; display: block; margin-bottom: 0.5rem; font-weight: 600;">Seleccionar Usuario del Sistema</label>
                <select id="newUserId" required style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; color: #1e293b;">
                    <option value="">-- Selecciona un usuario --</option>
                    @foreach($availableUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} - {{ $u->position }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="color: #64748b; font-size: 0.8rem; display: block; margin-bottom: 0.5rem; font-weight: 600;">Asignar Rol</label>
                <select id="newUserRole" required style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; color: #1e293b;">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $role->name == 'Usuario' ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeAddMemberModal()" class="btn" style="background: #f1f5f9; color: #64748b;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="background: #6366f1;">Dar Acceso</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Editar Usuario (Existente) -->
<div id="userModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 1rem; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
        <h3 id="modalTitle" style="color: #1e293b; font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Editar Rol</h3>
        <form id="userForm">
            @csrf
            <input type="hidden" id="userId">
            <div style="margin-bottom: 1rem;">
                <label style="color: #64748b; font-size: 0.8rem; display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre Completo</label>
                <input type="text" id="userName" readonly style="width: 100%; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; color: #475569;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="color: #64748b; font-size: 0.8rem; display: block; margin-bottom: 0.5rem; font-weight: 600;">Puesto (Sistema Tickets)</label>
                <input type="text" id="userPosition" readonly style="width: 100%; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; color: #475569;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="color: #64748b; font-size: 0.8rem; display: block; margin-bottom: 0.5rem; font-weight: 600;">Nuevo Rol</label>
                <select id="userRole" required style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; color: #1e293b;">
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeModal()" class="btn" style="background: #f1f5f9; color: #64748b;">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar Rol</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openAddMemberModal() {
        document.getElementById('addMemberModal').style.display = 'flex';
    }

    function closeAddMemberModal() {
        document.getElementById('addMemberModal').style.display = 'none';
    }

    document.getElementById('addMemberForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            user_id: document.getElementById('newUserId').value,
            role: document.getElementById('newUserRole').value,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route('users.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                Swal.fire('¡Éxito!', 'Acceso concedido correctamente', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.error || 'Ocurrió un error', 'error');
            }
        });
    });

    function closeModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    function editUser(user, roleName) {
        document.getElementById('modalTitle').innerText = 'Editar Usuario';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userPosition').value = user.position;
        document.getElementById('userRole').value = roleName || '';
        document.getElementById('userModal').style.display = 'flex';
    }

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const baseUrl = '{{ url('users') }}';
        const url = id ? `${baseUrl}/${id}` : baseUrl;
        const method = id ? 'PUT' : 'POST';

        const data = {
            role: document.getElementById('userRole').value,
            _token: '{{ csrf_token() }}'
        };

        fetch(url, {
            method: method,
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            const res = await response.json();
            if (response.ok && (res.success || !res.errors)) {
                Swal.fire('¡Éxito!', 'Usuario actualizado correctamente', 'success').then(() => location.reload());
            } else {
                let errorMsg = res.message || 'Ocurrió un error inesperado';
                if (res.errors) {
                    errorMsg = Object.values(res.errors).flat().join('<br>');
                }
                Swal.fire({
                    title: 'Error de Validación',
                    html: errorMsg,
                    icon: 'error'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo conectar con el servidor. Revisa tu conexión.', 'error');
        });
    });

    function toggleStatus(id) {
        const url = `{{ url('users') }}/${id}/toggle-status`;
        fetch(url, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(res => {
            if(res.success) {
                Swal.fire('Acceso Revocado', 'El usuario ya no tiene acceso al sistema', 'success').then(() => location.reload());
            }
        });
    }

    function deleteUser(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará al usuario permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = `{{ url('users') }}/${id}`;
                fetch(url, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    if(res.success) {
                        Swal.fire('Eliminado', 'El usuario ha sido eliminado.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.error, 'error');
                    }
                });
            }
        });
    }
</script>

<style>
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    /* Estilos para la paginado de Laravel */
    nav .flex.justify-between.flex-1.sm\:hidden { display: none; }
    nav span.relative.z-0.inline-flex.shadow-sm.rounded-md {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 4px;
        border-radius: 0.5rem;
    }
    nav span.relative.z-0.inline-flex.shadow-sm.rounded-md a, 
    nav span.relative.z-0.inline-flex.shadow-sm.rounded-md span {
        color: #475569 !important;
        background: transparent !important;
        border: none !important;
        padding: 0.5rem 0.75rem !important;
    }
    nav span.relative.z-0.inline-flex.shadow-sm.rounded-md span.cursor-default {
        background: #f1f5f9 !important;
        color: #6366f1 !important;
        font-weight: 700;
        border-radius: 0.375rem;
    }
    nav svg { width: 16px; color: #64748b !important; }
</style>
@endsection
