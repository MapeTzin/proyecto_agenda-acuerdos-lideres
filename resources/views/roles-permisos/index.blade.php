@extends('layouts.app')

@section('title', 'Usuarios, Roles y Permisos - Agenda de Acuerdos')
@section('header_title', 'Usuarios, Roles y Permisos')

@section('content')
<div class="roles-container">
    <!-- Header Card -->
    <div class="card header-card">
        <div class="header-main">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
                    <div class="header-icon-box">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: #1e293b; margin: 0;">Gestión de Accesos, Roles y Permisos</h2>
                        <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Administre los colaboradores autorizados para ingresar y sus facultades en el sistema</p>
                    </div>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-chip">
                    <span class="stat-number">{{ $totalUsersWithAccess }}</span>
                    <span class="stat-label">Con Acceso</span>
                </div>
                <div class="stat-chip">
                    <span class="stat-number">{{ $roles->count() }}</span>
                    <span class="stat-label">Roles</span>
                </div>
                <div class="stat-chip">
                    <span class="stat-number">{{ $permissions->count() }}</span>
                    <span class="stat-label">Permisos</span>
                </div>
            </div>
        </div>

        <!-- Tab Navigation (Usuarios, Roles del Sistema, Permisos) -->
        <div class="nav-tabs-wrapper">
            <button class="tab-btn active" onclick="switchTab('tab-users')" id="btn-tab-users">
                <i class="fas fa-users"></i> Usuarios
                <span class="badge-count">{{ $totalUsersWithAccess }}</span>
            </button>
            <button class="tab-btn" onclick="switchTab('tab-roles')" id="btn-tab-roles">
                <i class="fas fa-shield-alt"></i> Roles del Sistema
                <span class="badge-count">{{ $roles->count() }}</span>
            </button>
            <button class="tab-btn" onclick="switchTab('tab-permissions')" id="btn-tab-permissions">
                <i class="fas fa-key"></i> Permisos
                <span class="badge-count">{{ $permissions->count() }}</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: USUARIOS CON ACCESO -->
    <!-- ========================================== -->
    <div id="tab-users" class="tab-content active">

        <!-- Panel Superior: Selección de Usuario y Concesión de Acceso Inmediato -->
        <div class="card quick-grant-card">
            <div class="quick-grant-header">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div class="quick-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">
                            Dar Acceso a Usuario
                        </h3>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 0.15rem 0 0 0;">
                            Seleccione un colaborador registrado en <strong>Auth Center</strong> y asígnele su rol para habilitar su acceso al sistema
                        </p>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn-text-link" onclick="openGrantAccessModal()">
                        <i class="fas fa-sliders-h"></i> Concesión avanzada (permisos directos)
                    </button>
                </div>
            </div>

            <form id="quickGrantForm" class="quick-grant-form">
                @csrf
                <div class="grant-field-user">
                    <label class="grant-label" for="quickUserId">
                        <i class="fas fa-user-check"></i> Seleccionar Colaborador de Auth Center
                    </label>
                    <div class="select-search-container">
                        <select id="quickUserId" class="form-control-grant" required>
                            <option value="">-- Selecciona un colaborador disponible ({{ $availableAuthUsers->count() }} disponibles) --</option>
                            @foreach($availableAuthUsers as $u)
                                <option value="{{ $u->id }}" data-email="{{ $u->email }}" data-department="{{ $u->department_name }}" data-position="{{ $u->position ?? 'Sin Puesto' }}">
                                    {{ $u->name }} — {{ $u->department_name }} | {{ $u->position ?? 'Sin Puesto' }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grant-field-role">
                    <label class="grant-label" for="quickUserRole">
                        <i class="fas fa-id-badge"></i> Rol a Asignar
                    </label>
                    <select id="quickUserRole" class="form-control-grant" required>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ $r->name == 'Usuario' ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grant-field-submit">
                    <label class="grant-label" style="visibility: hidden;">Acción</label>
                    <button type="submit" class="btn btn-primary btn-grant" id="btnQuickGrant">
                        <i class="fas fa-check-circle"></i> Conceder Acceso
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Usuarios con Acceso -->
        <div class="card content-card">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Colaboradores con Acceso</h3>
                    <span class="badge-total">{{ $users->total() }} autorizados</span>
                </div>

                <form method="GET" action="{{ route('roles-permisos.index') }}" class="search-form" id="usersFilterForm">
                    <input type="hidden" name="tab" value="tab-users">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o correo..." class="form-control-custom">
                    </div>

                    <select name="role" class="form-select-custom" onchange="document.getElementById('usersFilterForm').submit()">
                        <option value="">-- Todos los Roles --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>

                    @if(request('search') || request('role'))
                        <a href="{{ route('roles-permisos.index') }}" class="btn-clear-filter" title="Limpiar Filtros">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Correo Electrónico</th>
                            <th>Área / Departamento</th>
                            <th>Rol en el Sistema</th>
                            <th>Estado de Acceso</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-avatar" style="background: {{ $user->area_color }};">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">
                                            {{ $user->name }}
                                            @if($user->email === 'soporte@mapetzin.com')
                                                <span class="badge-superadmin">Superadmin</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $user->position }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: 0.85rem; color: #475569;">{{ $user->email }}</td>
                            <td>
                                <span class="badge-area">
                                    {{ implode(' y ', $user->areas) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                    @forelse($user->roles as $role)
                                        <span class="badge-role badge-role-{{ strtolower(str_replace(' ', '-', $role->name)) }}">
                                            <i class="fas fa-shield-alt"></i> {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge-no-role">Sin Rol</span>
                                    @endforelse

                                    @if($user->permissions->count() > 0)
                                        <span class="badge-direct-permissions" title="{{ $user->permissions->pluck('name')->join(', ') }}">
                                            +{{ $user->permissions->count() }} perm. directo(s)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->auth_center_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle"></i> Acceso Activo
                                    </span>
                                @else
                                    <span class="status-badge status-warning" title="Tiene rol pero falta sincronizar en Auth Center">
                                        <i class="fas fa-exclamation-triangle"></i> Desincronizado
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                    <button onclick="openEditUserModal({{ json_encode($user) }}, {{ json_encode($user->roles->pluck('name')) }}, {{ json_encode($user->permissions->pluck('name')) }})"
                                            class="btn-action-icon btn-action-edit" title="Editar Rol y Permisos">
                                        <i class="fas fa-user-edit"></i>
                                    </button>

                                    @if($user->email !== 'soporte@mapetzin.com')
                                        <button onclick="revokeUserAccess({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                class="btn-action-icon btn-action-revoke" title="Revocar Acceso al Sistema">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8;">
                                <i class="fas fa-user-shield" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                No se encontraron colaboradores con acceso que coincidan con la búsqueda.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #f1f5f9;">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: ROLES DEL SISTEMA -->
    <!-- ========================================== -->
    <div id="tab-roles" class="tab-content">
        <div class="card content-card">
            <div class="filter-bar" style="justify-content: space-between;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">Roles y Niveles de Facultades</h3>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 0.2rem 0 0 0;">Defina los perfiles de usuario y los permisos que otorgan en cada módulo de la aplicación</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="openCreateRoleModal()">
                        <i class="fas fa-plus"></i> Crear Nuevo Rol
                    </button>
                </div>
            </div>

            <div class="roles-grid">
                @foreach($roles as $role)
                <div class="role-card role-card-{{ strtolower(str_replace(' ', '-', $role->name)) }}">
                    <div class="role-card-header">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="role-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">
                                    {{ $role->name }}
                                </h4>
                                <span class="role-type-tag">
                                    {{ in_array($role->name, ['Administrador', 'Director General', 'Usuario']) ? 'Rol del Sistema' : 'Personalizado' }}
                                </span>
                            </div>
                        </div>
                        <div class="role-user-count">
                            <i class="fas fa-users"></i> {{ $role->users_count }}
                        </div>
                    </div>

                    <div class="role-card-desc">
                        @if($role->name === 'Administrador')
                            Acceso total a todos los módulos operativos, planeador, reportes y administración de usuarios.
                        @elseif($role->name === 'Director General')
                            Consulta y visualización ejecutiva global de todas las áreas, dashboards e histórico.
                        @elseif($role->name === 'Usuario')
                            Operación estándar de acuerdos por área asignada, captura de avances y consulta de KPIs.
                        @else
                            Rol personalizado con asignación modular de permisos específicos.
                        @endif
                    </div>

                    <div class="role-card-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                Permisos Asignados
                            </span>
                            <span class="role-perms-count">{{ $role->permissions->count() }} de {{ $permissions->count() }}</span>
                        </div>
                        <div class="role-permissions-list">
                            @forelse($role->permissions as $p)
                                <span class="perm-tag">{{ $p->name }}</span>
                            @empty
                                <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Sin permisos asociados</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="role-card-footer">
                        <button class="btn btn-sm btn-outline" onclick="openEditRoleModal({{ json_encode($role) }}, {{ json_encode($role->permissions->pluck('name')) }})">
                            <i class="fas fa-sliders-h"></i> Configurar Permisos
                        </button>
                        @if(!in_array($role->name, ['Administrador', 'Director General', 'Usuario']))
                            <button class="btn btn-sm btn-danger-outline" onclick="deleteRole({{ $role->id }}, '{{ addslashes($role->name) }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: PERMISOS -->
    <!-- ========================================== -->
    <div id="tab-permissions" class="tab-content">
        <div class="card content-card">
            <div class="filter-bar" style="justify-content: space-between;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">Catálogo de Permisos Granulares</h3>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 0.2rem 0 0 0;">Funcionalidades controladas por el sistema organizadas por módulo de operación</p>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-secondary" onclick="syncSystemPermissions()" title="Restaura o sincroniza los permisos base">
                        <i class="fas fa-sync-alt"></i> Sincronizar Permisos Base
                    </button>
                    <button class="btn btn-primary" onclick="openCreatePermissionModal()">
                        <i class="fas fa-plus"></i> Nuevo Permiso
                    </button>
                </div>
            </div>

            <div class="modules-grid">
                @foreach($permissionsGrouped as $moduleName => $modulePerms)
                <div class="module-card">
                    <div class="module-card-header">
                        <div class="module-icon">
                            @if(str_contains($moduleName, 'Dashboard'))
                                <i class="fas fa-chart-pie"></i>
                            @elseif(str_contains($moduleName, 'KPI'))
                                <i class="fas fa-tachometer-alt"></i>
                            @elseif(str_contains($moduleName, 'Acuerdos'))
                                <i class="fas fa-tasks"></i>
                            @elseif(str_contains($moduleName, 'Planeador'))
                                <i class="fas fa-calendar-alt"></i>
                            @else
                                <i class="fas fa-shield-alt"></i>
                            @endif
                        </div>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;">{{ $moduleName }}</h4>
                        <span class="badge-count" style="margin-left: auto;">{{ count($modulePerms) }}</span>
                    </div>
                    <div class="module-card-body">
                        @foreach($modulePerms as $perm)
                        <div class="perm-row">
                            <div class="perm-indicator">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <span class="perm-code">{{ $perm->name }}</span>
                                <span class="perm-desc">
                                    @if($perm->name === 'dashboard.view') Ver métricas ejecutivas en Dashboard Corporativo
                                    @elseif($perm->name === 'seguimiento.view') Consulta global del avance de todas las áreas
                                    @elseif($perm->name === 'kpis.view') Visualizar indicadores y KPIs por área
                                    @elseif($perm->name === 'kpis.manage') Registrar y modificar valores de KPIs semanales
                                    @elseif($perm->name === 'acuerdos.view') Ver acuerdos correspondientes a su área
                                    @elseif($perm->name === 'acuerdos.create') Crear acuerdos nuevos
                                    @elseif($perm->name === 'acuerdos.edit') Actualizar estados y registrar avances
                                    @elseif($perm->name === 'acuerdos.delete') Eliminar acuerdos
                                    @elseif($perm->name === 'acuerdos.export') Exportar listado de acuerdos a Excel
                                    @elseif($perm->name === 'acuerdos.historico.view') Consultar histórico de acuerdos cerrados
                                    @elseif($perm->name === 'planeador.view') Visualizar eventos del planeador
                                    @elseif($perm->name === 'planeador.manage') Crear, editar o importar calendarios
                                    @elseif($perm->name === 'users.view') Consultar lista de usuarios autorizados
                                    @elseif($perm->name === 'users.manage') Dar y revocar accesos a usuarios de Auth Center
                                    @elseif($perm->name === 'roles.manage') Gestionar roles y catálogo de permisos
                                    @else Permiso funcional del sistema
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODALES -->
<!-- ========================================== -->

<!-- Modal: Concesión Avanzada de Acceso -->
<div id="grantAccessModal" class="modal-overlay" style="display: none;">
    <div class="modal-dialog modal-dialog-lg">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                <i class="fas fa-user-plus" style="color: #6366f1; margin-right: 0.4rem;"></i> Dar Acceso a Usuario de Auth Center
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeGrantAccessModal()">&times;</button>
        </div>
        <form id="grantAccessForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Seleccionar Colaborador de Auth Center</label>
                    <select id="modalGrantUserId" class="form-control-custom" required style="width: 100%;">
                        <option value="">-- Selecciona un colaborador --</option>
                        @foreach($availableAuthUsers as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->name }} — {{ $u->department_name }} | {{ $u->position ?? 'Sin Puesto' }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Rol Principal</label>
                    <div class="role-selector-radios">
                        @foreach($roles as $role)
                            <label class="role-radio-card">
                                <input type="radio" name="modal_grant_role" value="{{ $role->name }}" {{ $role->name == 'Usuario' ? 'checked' : '' }}>
                                <div class="role-radio-content">
                                    <div class="role-radio-title">
                                        <i class="fas fa-shield-alt"></i> {{ $role->name }}
                                    </div>
                                    <span class="role-radio-desc">
                                        {{ $role->permissions->count() }} permisos asociados
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <label class="form-label" style="margin: 0;">Permisos Adicionales Directos (Opcional)</label>
                        <span style="font-size: 0.72rem; color: #64748b;">Asigna facultades adicionales a las otorgadas por el rol</span>
                    </div>
                    <div class="perms-selector-box">
                        @foreach($permissionsGrouped as $modName => $pList)
                            <div class="perm-module-subgroup">
                                <strong>{{ $modName }}</strong>
                                <div class="perm-checkbox-grid">
                                    @foreach($pList as $p)
                                        <label class="perm-checkbox-item">
                                            <input type="checkbox" name="grant_permissions[]" value="{{ $p->name }}">
                                            <span>{{ $p->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeGrantAccessModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitGrant">
                    <i class="fas fa-check-circle"></i> Otorgar Acceso
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Editar Rol y Permisos de Usuario -->
<div id="editUserModal" class="modal-overlay" style="display: none;">
    <div class="modal-dialog modal-dialog-lg">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                <i class="fas fa-user-edit" style="color: #6366f1; margin-right: 0.4rem;"></i> Editar Rol y Permisos del Colaborador
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeEditUserModal()">&times;</button>
        </div>
        <form id="editUserForm">
            @csrf
            <input type="hidden" id="editUserId">
            <div class="modal-body">
                <div class="user-summary-card">
                    <div class="user-summary-avatar" id="editUserAvatar">US</div>
                    <div>
                        <div style="font-size: 1rem; font-weight: 700; color: #1e293b;" id="editUserName">Nombre</div>
                        <div style="font-size: 0.8rem; color: #64748b;" id="editUserEmail">correo@mapetzin.com</div>
                        <div style="font-size: 0.75rem; color: #475569;" id="editUserPosition">Puesto</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Rol Asignado</label>
                    <div class="role-selector-radios">
                        @foreach($roles as $role)
                            <label class="role-radio-card">
                                <input type="radio" name="edit_user_role" value="{{ $role->name }}" class="edit-role-radio">
                                <div class="role-radio-content">
                                    <div class="role-radio-title">
                                        <i class="fas fa-shield-alt"></i> {{ $role->name }}
                                    </div>
                                    <span class="role-radio-desc">
                                        {{ $role->permissions->count() }} permisos asociados
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Permisos Directos Específicos</label>
                    <div class="perms-selector-box">
                        @foreach($permissionsGrouped as $modName => $pList)
                            <div class="perm-module-subgroup">
                                <strong>{{ $modName }}</strong>
                                <div class="perm-checkbox-grid">
                                    @foreach($pList as $p)
                                        <label class="perm-checkbox-item">
                                            <input type="checkbox" name="edit_permissions[]" value="{{ $p->name }}" class="edit-perm-check">
                                            <span>{{ $p->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditUserModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitEditUser">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Crear / Editar Rol -->
<div id="roleModal" class="modal-overlay" style="display: none;">
    <div class="modal-dialog modal-dialog-lg">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;" id="roleModalTitle">
                <i class="fas fa-shield-alt" style="color: #6366f1; margin-right: 0.4rem;"></i> Crear Rol
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeRoleModal()">&times;</button>
        </div>
        <form id="roleForm">
            @csrf
            <input type="hidden" id="roleId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre del Rol</label>
                    <input type="text" id="roleName" class="form-control-custom" placeholder="Ej. Supervisor de Operaciones" required>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label class="form-label" style="margin: 0;">Permisos Asignados a este Rol</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="button" class="btn-text" onclick="toggleAllRolePerms(true)">Marcar Todos</button>
                            <span style="color: #cbd5e1;">|</span>
                            <button type="button" class="btn-text" onclick="toggleAllRolePerms(false)">Desmarcar Todos</button>
                        </div>
                    </div>

                    <div class="perms-selector-box" style="max-height: 350px;">
                        @foreach($permissionsGrouped as $modName => $pList)
                            <div class="perm-module-subgroup">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong>{{ $modName }}</strong>
                                    <button type="button" class="btn-text-sm" onclick="toggleGroupPerms(this)">Alternar Grupo</button>
                                </div>
                                <div class="perm-checkbox-grid">
                                    @foreach($pList as $p)
                                        <label class="perm-checkbox-item">
                                            <input type="checkbox" name="role_permissions[]" value="{{ $p->name }}" class="role-perm-check">
                                            <span>{{ $p->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRoleModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitRole">
                    <i class="fas fa-save"></i> Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Crear Permiso Individual -->
<div id="permissionModal" class="modal-overlay" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                <i class="fas fa-key" style="color: #6366f1; margin-right: 0.4rem;"></i> Crear Nuevo Permiso
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeCreatePermissionModal()">&times;</button>
        </div>
        <form id="permissionForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre del Permiso (Código)</label>
                    <input type="text" id="permissionName" class="form-control-custom" placeholder="modulo.accion (ej. reportes.exportar)" required>
                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                        Se recomienda usar minúsculas y punto para separar el módulo y la acción (ej. <code>acuerdos.revisar</code>).
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCreatePermissionModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Registrar Permiso
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 & Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Tab switching
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

        const targetTab = document.getElementById(tabId);
        const targetBtn = document.getElementById('btn-' + tabId);
        if (targetTab && targetBtn) {
            targetTab.classList.add('active');
            targetBtn.classList.add('active');
        }

        window.location.hash = tabId;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '');
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || hash;
        if (tabParam && document.getElementById(tabParam)) {
            switchTab(tabParam);
        }
    });

    // 1. Concesión rápida desde el panel superior
    document.getElementById('quickGrantForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const userId = document.getElementById('quickUserId').value;
        const role = document.getElementById('quickUserRole').value;

        if (!userId) {
            Swal.fire('Atención', 'Por favor selecciona un colaborador de la lista.', 'warning');
            return;
        }

        const btn = document.getElementById('btnQuickGrant');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Concediendo...';

        fetch('{{ route('roles-permisos.grant-access') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                user_id: userId,
                role: role,
                _token: '{{ csrf_token() }}'
            })
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Conceder Acceso';
            if (res.success) {
                Swal.fire({
                    title: '¡Acceso Concedido!',
                    text: res.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'No se pudo otorgar el acceso.', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Conceder Acceso';
            Swal.fire('Error', 'Fallo en la comunicación con el servidor.', 'error');
        });
    });

    // Modal Helpers
    function openGrantAccessModal() {
        document.getElementById('grantAccessForm').reset();
        document.getElementById('grantAccessModal').style.display = 'flex';
    }
    function closeGrantAccessModal() {
        document.getElementById('grantAccessModal').style.display = 'none';
    }

    function openEditUserModal(user, roles, permissions) {
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editUserName').innerText = user.name;
        document.getElementById('editUserEmail').innerText = user.email;
        document.getElementById('editUserPosition').innerText = user.position || 'Sin Puesto';
        document.getElementById('editUserAvatar').innerText = user.name.substring(0, 2).toUpperCase();

        const currentRole = (roles && roles.length) ? roles[0] : 'Usuario';
        document.querySelectorAll('.edit-role-radio').forEach(rb => {
            rb.checked = (rb.value === currentRole);
        });

        document.querySelectorAll('.edit-perm-check').forEach(cb => {
            cb.checked = permissions && permissions.includes(cb.value);
        });

        document.getElementById('editUserModal').style.display = 'flex';
    }
    function closeEditUserModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    function openCreateRoleModal() {
        document.getElementById('roleModalTitle').innerHTML = '<i class="fas fa-shield-alt" style="color: #6366f1; margin-right: 0.4rem;"></i> Crear Nuevo Rol';
        document.getElementById('roleId').value = '';
        document.getElementById('roleName').value = '';
        document.getElementById('roleName').removeAttribute('readonly');
        document.querySelectorAll('.role-perm-check').forEach(cb => cb.checked = false);
        document.getElementById('roleModal').style.display = 'flex';
    }

    function openEditRoleModal(role, permissions) {
        document.getElementById('roleModalTitle').innerHTML = '<i class="fas fa-sliders-h" style="color: #6366f1; margin-right: 0.4rem;"></i> Configurar Rol: ' + role.name;
        document.getElementById('roleId').value = role.id;
        document.getElementById('roleName').value = role.name;

        if (['Administrador', 'Director General', 'Usuario'].includes(role.name)) {
            document.getElementById('roleName').setAttribute('readonly', 'readonly');
        } else {
            document.getElementById('roleName').removeAttribute('readonly');
        }

        document.querySelectorAll('.role-perm-check').forEach(cb => {
            cb.checked = permissions && permissions.includes(cb.value);
        });

        document.getElementById('roleModal').style.display = 'flex';
    }
    function closeRoleModal() {
        document.getElementById('roleModal').style.display = 'none';
    }

    function openCreatePermissionModal() {
        document.getElementById('permissionForm').reset();
        document.getElementById('permissionModal').style.display = 'flex';
    }
    function closeCreatePermissionModal() {
        document.getElementById('permissionModal').style.display = 'none';
    }

    function toggleAllRolePerms(check) {
        document.querySelectorAll('.role-perm-check').forEach(cb => cb.checked = check);
    }

    function toggleGroupPerms(btn) {
        const subgroup = btn.closest('.perm-module-subgroup');
        const checkboxes = subgroup.querySelectorAll('.role-perm-check');
        const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
        checkboxes.forEach(cb => cb.checked = anyUnchecked);
    }

    // Modal Concesión Avanzada
    document.getElementById('grantAccessForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const selectedPerms = Array.from(document.querySelectorAll('input[name="grant_permissions[]"]:checked')).map(cb => cb.value);
        const selectedRoleEl = document.querySelector('input[name="modal_grant_role"]:checked');

        const data = {
            user_id: document.getElementById('modalGrantUserId').value,
            role: selectedRoleEl ? selectedRoleEl.value : 'Usuario',
            permissions: selectedPerms,
            _token: '{{ csrf_token() }}'
        };

        const btn = document.getElementById('btnSubmitGrant');
        btn.disabled = true;

        fetch('{{ route('roles-permisos.grant-access') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            if (res.success) {
                Swal.fire('¡Éxito!', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'No se pudo otorgar el acceso.', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            Swal.fire('Error', 'Fallo en la comunicación con el servidor.', 'error');
        });
    });

    // Modal Editar Usuario
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const userId = document.getElementById('editUserId').value;
        const selectedPerms = Array.from(document.querySelectorAll('input[name="edit_permissions[]"]:checked')).map(cb => cb.value);
        const selectedRoleEl = document.querySelector('input[name="edit_user_role"]:checked');

        const data = {
            role: selectedRoleEl ? selectedRoleEl.value : 'Usuario',
            permissions: selectedPerms,
            _token: '{{ csrf_token() }}'
        };

        const btn = document.getElementById('btnSubmitEditUser');
        btn.disabled = true;

        fetch(`{{ url('roles-permisos/user') }}/${userId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            if (res.success) {
                Swal.fire('¡Actualizado!', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'Error al actualizar el usuario.', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
        });
    });

    // Revocar Acceso
    function revokeUserAccess(userId, userName) {
        Swal.fire({
            title: '¿Revocar acceso?',
            html: `Se removerán los roles en el sistema y se desactivará el acceso en <strong>Auth Center</strong> para <strong>${userName}</strong>.<br><br>El usuario ya no podrá iniciar sesión en la plataforma.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, revocar acceso',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('roles-permisos/user') }}/${userId}/revoke`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Acceso Revocado', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message || 'No se pudo revocar el acceso.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Error de comunicación.', 'error'));
            }
        });
    }

    // Guardar Rol
    document.getElementById('roleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const roleId = document.getElementById('roleId').value;
        const selectedPerms = Array.from(document.querySelectorAll('.role-perm-check:checked')).map(cb => cb.value);

        const data = {
            name: document.getElementById('roleName').value,
            permissions: selectedPerms,
            _token: '{{ csrf_token() }}'
        };

        const url = roleId ? `{{ url('roles-permisos/roles') }}/${roleId}` : '{{ route('roles-permisos.roles.store') }}';
        const method = roleId ? 'PUT' : 'POST';

        const btn = document.getElementById('btnSubmitRole');
        btn.disabled = true;

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            if (res.success) {
                Swal.fire('¡Éxito!', res.message, 'success').then(() => {
                    window.location.hash = 'tab-roles';
                    location.reload();
                });
            } else {
                Swal.fire('Error', res.message || 'Error al guardar el rol.', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            Swal.fire('Error', 'Error de red.', 'error');
        });
    });

    // Eliminar Rol
    function deleteRole(roleId, roleName) {
        Swal.fire({
            title: '¿Eliminar Rol?',
            html: `¿Está seguro de eliminar el rol <strong>${roleName}</strong>? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('roles-permisos/roles') }}/${roleId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Eliminado', res.message, 'success').then(() => {
                            window.location.hash = 'tab-roles';
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Error de red.', 'error'));
            }
        });
    }

    // Crear Permiso
    document.getElementById('permissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            name: document.getElementById('permissionName').value,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route('roles-permisos.permissions.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire('¡Creado!', res.message, 'success').then(() => {
                    window.location.hash = 'tab-permissions';
                    location.reload();
                });
            } else {
                Swal.fire('Error', res.message || 'Error al crear el permiso.', 'error');
            }
        });
    });

    // Sincronizar Permisos Base
    function syncSystemPermissions() {
        Swal.fire({
            title: 'Sincronizar Permisos Base',
            text: 'Se restaurarán y vincularán los 15 permisos base del sistema con los roles predeterminados.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Sincronizar Ahora',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('roles-permisos.permissions.sync') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Sincronizado', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    }
</script>

<style>
    .roles-container {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .header-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        padding: 1.5rem 1.75rem 0 1.75rem;
    }

    .header-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .header-icon-box {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.25);
    }

    .header-stats {
        display: flex;
        gap: 0.75rem;
    }

    .stat-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.4rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-number {
        font-size: 1.15rem;
        font-weight: 800;
        color: #4f46e5;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
    }

    /* Tab Navigation */
    .nav-tabs-wrapper {
        display: flex;
        gap: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 0;
    }

    .tab-btn {
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.85rem 1.4rem;
        font-size: 0.92rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.2s ease;
    }

    .tab-btn:hover {
        color: #1e293b;
    }

    .tab-btn.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: #f8faff;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        font-weight: 700;
    }

    .badge-count {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 1rem;
    }

    .tab-btn.active .badge-count {
        background: #e0e7ff;
        color: #4338ca;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Quick Grant Card */
    .quick-grant-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
        border: 1px solid #c7d2fe;
        border-radius: 1rem;
        padding: 1.25rem 1.75rem;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.05);
        margin-bottom: 1.25rem;
    }

    .quick-grant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .quick-icon {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .btn-text-link {
        background: none;
        border: none;
        color: #6366f1;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0;
    }
    .btn-text-link:hover {
        text-decoration: underline;
        color: #4f46e5;
    }

    .quick-grant-form {
        display: grid;
        grid-template-columns: 1fr 220px 180px;
        gap: 1rem;
        align-items: flex-end;
    }

    @media (max-width: 900px) {
        .quick-grant-form {
            grid-template-columns: 1fr;
        }
    }

    .grant-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.4rem;
    }

    .form-control-grant {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        font-size: 0.86rem;
        background: white;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }

    .form-control-grant:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .btn-grant {
        width: 100%;
        padding: 0.65rem 1rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.86rem;
        background: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);
    }
    .btn-grant:hover {
        background: #4338ca;
        transform: translateY(-1px);
    }

    /* Content Card */
    .content-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .filter-bar {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .badge-total {
        font-size: 0.72rem;
        font-weight: 700;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 0.2rem 0.55rem;
        border-radius: 1rem;
    }

    .search-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-input-wrapper {
        position: relative;
        min-width: 260px;
    }

    .search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .form-control-custom {
        width: 100%;
        padding: 0.55rem 0.85rem 0.55rem 2.2rem;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        font-size: 0.85rem;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
    }

    .form-control-custom:focus {
        border-color: #6366f1;
        background: white;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .form-select-custom {
        padding: 0.55rem 0.85rem;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        font-size: 0.85rem;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
    }

    .btn-clear-filter {
        font-size: 0.8rem;
        color: #ef4444;
        text-decoration: none;
        padding: 0.4rem 0.6rem;
        border-radius: 0.4rem;
        background: #fef2f2;
    }

    /* Custom Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th {
        padding: 0.9rem 1.25rem;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
    }

    .custom-table td {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .custom-table tbody tr:hover {
        background: #f8fafc;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .badge-superadmin {
        font-size: 0.65rem;
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
        padding: 0.1rem 0.4rem;
        border-radius: 0.25rem;
        font-weight: 700;
        margin-left: 0.3rem;
    }

    .badge-area {
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
    }

    .badge-role {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 0.35rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-role-administrador {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .badge-role-director-general {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }

    .badge-role-usuario {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .badge-no-role {
        font-size: 0.7rem;
        background: #f1f5f9;
        color: #94a3b8;
        padding: 0.2rem 0.5rem;
        border-radius: 0.35rem;
    }

    .badge-direct-permissions {
        font-size: 0.65rem;
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 0.2rem 0.45rem;
        border-radius: 0.35rem;
        cursor: help;
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .status-active {
        background: #ecfdf5;
        color: #059669;
    }

    .status-warning {
        background: #fffbeb;
        color: #d97706;
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 0.4rem;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 0.85rem;
    }

    .btn-action-edit {
        background: #eef2ff;
        color: #4f46e5;
    }
    .btn-action-edit:hover {
        background: #e0e7ff;
        transform: translateY(-1px);
    }

    .btn-action-revoke {
        background: #fef2f2;
        color: #ef4444;
    }
    .btn-action-revoke:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }

    /* Roles Cards */
    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.25rem;
        padding: 1.5rem;
    }

    .role-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-top: 4px solid #6366f1;
    }

    .role-card-administrador { border-top-color: #ef4444; }
    .role-card-director-general { border-top-color: #8b5cf6; }
    .role-card-usuario { border-top-color: #10b981; }

    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
    }

    .role-card-header {
        padding: 1.25rem 1.25rem 0.5rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .role-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        background: #f8fafc;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 1px solid #e2e8f0;
    }

    .role-type-tag {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        background: #f1f5f9;
        padding: 0.15rem 0.45rem;
        border-radius: 0.25rem;
        display: inline-block;
        margin-top: 0.2rem;
    }

    .role-user-count {
        font-size: 0.78rem;
        color: #475569;
        font-weight: 700;
        background: #f1f5f9;
        padding: 0.25rem 0.6rem;
        border-radius: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .role-card-desc {
        padding: 0 1.25rem 0.75rem 1.25rem;
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.4;
    }

    .role-card-body {
        padding: 0.75rem 1.25rem;
        background: #f8fafc;
        flex: 1;
        border-top: 1px solid #f1f5f9;
    }

    .role-perms-count {
        font-size: 0.72rem;
        font-weight: 700;
        color: #6366f1;
    }

    .role-permissions-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        max-height: 130px;
        overflow-y: auto;
    }

    .perm-tag {
        font-size: 0.68rem;
        font-family: monospace;
        background: white;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 0.15rem 0.45rem;
        border-radius: 0.3rem;
    }

    .role-card-footer {
        padding: 0.85rem 1.25rem;
        background: white;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom-left-radius: 0.85rem;
        border-bottom-right-radius: 0.85rem;
    }

    /* Modules & Permissions Grid */
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 1.25rem;
        padding: 1.5rem;
    }

    .module-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }

    .module-card-header {
        background: #f8fafc;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .module-icon {
        width: 28px;
        height: 28px;
        border-radius: 0.4rem;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    .module-card-body {
        padding: 0.75rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .perm-row {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid #f8fafc;
    }
    .perm-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .perm-indicator {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ecfdf5;
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .perm-code {
        font-family: monospace;
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
        display: block;
    }

    .perm-desc {
        font-size: 0.75rem;
        color: #64748b;
        display: block;
        line-height: 1.3;
    }

    /* Role Radio Cards in Modals */
    .role-selector-radios {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.6rem;
    }

    .role-radio-card {
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        padding: 0.6rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        cursor: pointer;
        background: #f8fafc;
        transition: all 0.15s ease;
    }

    .role-radio-card:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .role-radio-card input[type="radio"]:checked + .role-radio-content {
        color: #4f46e5;
    }

    .role-radio-title {
        font-weight: 700;
        font-size: 0.85rem;
        color: #1e293b;
    }

    .role-radio-desc {
        font-size: 0.7rem;
        color: #64748b;
        display: block;
    }

    .user-summary-card {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
    }

    .user-summary-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #4f46e5;
        color: white;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    /* Buttons */
    .btn-outline {
        background: white;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 0.4rem 0.75rem;
        border-radius: 0.4rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .btn-danger-outline {
        background: white;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 0.4rem 0.6rem;
        border-radius: 0.4rem;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .btn-danger-outline:hover {
        background: #fef2f2;
    }

    .btn-text {
        background: none;
        border: none;
        color: #4f46e5;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
    }
    .btn-text:hover {
        text-decoration: underline;
    }

    .btn-text-sm {
        background: none;
        border: none;
        color: #64748b;
        font-size: 0.7rem;
        cursor: pointer;
        padding: 0;
    }

    /* Modals */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal-dialog {
        background: white;
        width: 100%;
        max-width: 580px;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: modalFadeIn 0.2s ease-out;
    }

    .modal-dialog-lg {
        max-width: 780px;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #94a3b8;
        cursor: pointer;
        line-height: 1;
    }
    .modal-close-btn:hover {
        color: #1e293b;
    }

    .modal-body {
        padding: 1.5rem 1.75rem;
        max-height: calc(85vh - 140px);
        overflow-y: auto;
    }

    .modal-footer {
        padding: 1rem 1.75rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.4rem;
    }

    .perms-selector-box {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        background: #f8fafc;
        max-height: 250px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .perm-module-subgroup {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.4rem;
        padding: 0.75rem;
    }

    .perm-module-subgroup strong {
        font-size: 0.8rem;
        color: #1e293b;
        display: block;
        margin-bottom: 0.5rem;
    }

    .perm-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.4rem;
    }

    .perm-checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        color: #475569;
        cursor: pointer;
    }

    .perm-checkbox-item input[type="checkbox"] {
        cursor: pointer;
    }
</style>
@endsection
