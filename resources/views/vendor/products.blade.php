@extends('layouts.vendor')

@section('content')

<main id="products-view" class="view active-view">
    <header class="main-header">
        <h1>MANAGE PRODUCTS</h1>
        <div class="header-icons-wrapper">
            <!-- You can link the bell/user icons to their respective routes later -->
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>
    
    <section class="content-container">
        <div class="content-header">
            <h2>All Products</h2>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="product-search" placeholder="Search products..." autocomplete="off">
                </div>
                <button class="action-button" id="new-product-btn">+ Add New Product</button>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    @forelse($products as $product)
                        <!-- 
                           We store product data in data-attributes on the row 
                           so our JavaScript can easily populate the 'Edit' modal 
                        -->
                        <tr data-id="{{ $product->id }}" 
                            data-name="{{ $product->name }}"
                            data-description="{{ $product->description }}"
                            data-price="{{ $product->price }}"
                            data-image-url="{{ Storage::url($product->image) }}"
                            data-update-url="{{ route('products.update', $product->id) }}"
                            data-delete-url="{{ route('products.destroy', $product->id) }}">
                            
                            <td>
                                <img src="{{ Storage::url($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="product-image" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ Str::limit($product->description, 50) }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>
                                <button class="table-action-btn edit-product-btn">Edit</button>
                                <button class="table-action-btn remove-product-btn" style="color: #D32F2F;">Remove</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 2rem;">
                                No products yet. Click "Add New Product" to start selling!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- PRODUCT FORM MODAL -->
<div id="product-form-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button class="modal-close-btn" data-close-modal> &times; </button>
        <h2 class="modal-title" style="margin-bottom: 2rem;" id="product-modal-title">Add New Product</h2>
        
        <form class="styled-form" id="product-form" enctype="multipart/form-data">
            <!-- Hidden input to store ID for editing -->
            <input type="hidden" name="product_id" id="product_id">
            
            <div class="form-group">
                <label for="p_name">Name</label>
                <input name="name" id="p_name" type="text" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="p_description">Description</label>
                <textarea name="description" id="p_description" rows="3" required autocomplete="off"></textarea>
            </div>
            
            <div class="form-group">
                <label for="p_price">Price (₱)</label>
                <input name="price" id="p_price" type="number" step="0.01" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="p_image">Image</label>
                <input name="image" id="p_image" type="file" accept="image/*">
            </div>
            
            <!-- Image Preview -->
            <div style="display: flex; justify-content: center;">
                <img id="image-preview" class="image-preview" alt="Image Preview" 
                     style="display:none; max-width: 150px; max-height: 150px; margin-top: 10px; border-radius: 8px; object-fit: cover;">
            </div>
            
            <div class="form-actions" style="justify-content: flex-end;">
                <button type="submit" class="action-button" id="save-product-btn">Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection