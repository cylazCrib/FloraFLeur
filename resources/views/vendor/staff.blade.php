@extends('layouts.vendor')

@section('content')
<main id="staff-view" class="view active-view">
    <header class="main-header">
        <h1>MANAGE STAFF</h1>
        <div class="header-icons-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>
    
    <section class="content-container">
        <div class="content-header">
            <h2>Shop Administrators & Staff</h2>
            <button class="action-button" id="add-staff-btn">+ Add Staff</button>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="staff-table-body">
                    @forelse($staffMembers as $staff)
                        <tr data-id="{{ $staff->id }}"
                            data-name="{{ $staff->name }}"
                            data-email="{{ $staff->email }}"
                            data-phone="{{ $staff->phone }}"
                            data-role="{{ $staff->role }}"
                            data-update-url="{{ route('vendor.staff.update', $staff->id) }}">
                            
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->phone }}</td>
                            <td>
                                <span class="status status-{{ Str::slug($staff->role) }}">
                                    {{ $staff->role }}
                                </span>
                            </td>
                            <td>
                                <span class="status status-{{ $staff->status === 'Active' ? 'approved' : 'suspended' }}">
                                    {{ $staff->status }}
                                </span>
                            </td>
                            <td>
                                <button class="table-action-btn edit-staff-btn">Edit</button>
                                <button class="table-action-btn toggle-status-btn" 
                                        data-url="{{ route('vendor.staff.toggle', $staff->id) }}">
                                    {{ $staff->status === 'Active' ? 'Suspend' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 2rem;">
                                No staff members yet. Click "Add Staff" to invite someone.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="staff-form-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button class="modal-close-btn" data-close-modal> &times; </button>
        <h2 class="modal-title" style="margin-bottom: 2rem;" id="staff-modal-title">Add New Staff</h2>
        
        <form class="styled-form" id="staff-form">
            <input type="hidden" name="staff_id" id="staff_id">
            
            <div class="form-group">
                <label for="s_name">Name</label>
                <input name="name" id="s_name" type="text" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="s_email">Email</label>
                <input name="email" id="s_email" type="email" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="s_phone">Phone</label>
                <input name="phone" id="s_phone" type="tel" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="s_role">Role</label>
                <select name="role" id="s_role" required>
                    <option value="Manager">Manager</option>
                    <option value="Florist">Florist</option>
                    <option value="Driver">Driver</option>
                </select>
            </div>
            
            <div class="form-actions" style="justify-content: flex-end;">
                <button type="submit" class="action-button">Save Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection