@extends('layouts.vendor')

@section('content')
<main id="inventory-view" class="view active-view">
    <header class="main-header">
        <h1>MANAGE INVENTORY</h1>
        <div class="header-icons-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>

    <section class="content-container" style="margin-bottom: 2rem;">
        <div class="content-header">
            <h2>Items Inventory</h2>
            <button class="action-button" id="add-item-btn" data-type="item">+ Add New Item</button>
        </div>
        <div class="inventory-list">
            @forelse($items as $item)
                @include('vendor.partials.inventory-item', ['item' => $item])
            @empty
                <p class="text-center text-gray-500">No items yet.</p>
            @endforelse
        </div>
    </section>

    <section class="content-container">
        <div class="content-header">
            <h2>Flowers Inventory</h2>
            <button class="action-button" id="add-flower-btn" data-type="flower">+ Add New Flower</button>
        </div>
        <div class="inventory-list">
            @forelse($flowers as $item)
                @include('vendor.partials.inventory-item', ['item' => $item])
            @empty
                <p class="text-center text-gray-500">No flowers yet.</p>
            @endforelse
        </div>
    </section>
</main>

<div id="item-form-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button class="modal-close-btn" data-close-modal> &times; </button>
        <h2 class="modal-title" style="margin-bottom: 2rem;" id="item-modal-title">Add New Item</h2>
        
        <form class="styled-form" id="item-form">
            <input type="hidden" name="item_id" id="item_id">
            <input type="hidden" name="type" id="item_type">
            
            <div class="form-group">
                <label>Item Name</label>
                <input name="name" id="i_name" type="text" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Code (Optional)</label>
                <input name="code" id="i_code" type="text" autocomplete="off">
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input name="quantity" id="i_quantity" type="number" min="0" required>
            </div>
            
            <div class="form-actions" style="justify-content: flex-end;">
                <button type="submit" class="action-button">Save Item</button>
            </div>
        </form>
    </div>
</div>
@endsection