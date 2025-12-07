document.addEventListener('DOMContentLoaded', function() {
    
    // --- CSRF Token Setup ---
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // --- Elements ---
    const toast = document.getElementById('toast');
    
    // --- Helper Functions ---
    function showToast(message) {
        if (toast) {
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    }

    function toggleModal(modal, show = true) {
        if(modal) modal.style.display = show ? 'flex' : 'none';
    }

    // ============================================================
    //  1. PRODUCT MANAGEMENT (CRUD & Search)
    // ============================================================
    const productModal = document.getElementById('product-form-modal');
    const productForm = document.getElementById('product-form');
    
    // A. Search Functionality (Client-side)
    const productSearch = document.getElementById('product-search');
    if (productSearch) {
        productSearch.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#products-table-body tr');
            
            rows.forEach(row => {
                // Ensure row has data attributes before checking
                if(row.dataset.name) {
                    const name = row.dataset.name.toLowerCase();
                    const desc = row.dataset.description ? row.dataset.description.toLowerCase() : '';
                    
                    if (name.includes(term) || desc.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }

    // ============================================================
    //  GLOBAL CLICK LISTENER (Delegation)
    // ============================================================
    document.body.addEventListener('click', function(e) {
        const target = e.target;

        // --- ADD PRODUCT ---
        if (target.id === 'new-product-btn') {
            e.preventDefault();
            if(productForm) {
                productForm.reset();
                document.getElementById('product_id').value = ''; 
                const preview = document.getElementById('image-preview');
                if(preview) { preview.src = ''; preview.style.display = 'none'; }
                
                document.getElementById('product-modal-title').textContent = 'Add New Product';
                document.getElementById('save-product-btn').textContent = 'Add Product';
                // Set URL for creating new product
                productForm.dataset.url = '/vendor/products'; 
            }
            toggleModal(productModal, true);
        }

        // --- EDIT PRODUCT ---
        if (target.closest('.edit-product-btn')) {
            const row = target.closest('tr');
            const data = row.dataset;
            
            // Populate Form
            document.getElementById('product_id').value = data.id;
            document.getElementById('p_name').value = data.name;
            document.getElementById('p_description').value = data.description;
            document.getElementById('p_price').value = data.price;
            
            // Show Image Preview
            const preview = document.getElementById('image-preview');
            if(preview) { 
                preview.src = data.imageUrl; 
                preview.style.display = 'block'; 
            }

            document.getElementById('product-modal-title').textContent = 'Edit Product';
            document.getElementById('save-product-btn').textContent = 'Update Product';
            // Set URL for updating specific product
            productForm.dataset.url = data.updateUrl;
            toggleModal(productModal, true);
        }

        // --- DELETE PRODUCT ---
        if (target.closest('.remove-product-btn')) {
            if (!confirm('Are you sure you want to delete this product?')) return;
            const row = target.closest('tr');
            
            fetch(row.dataset.deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => { 
                showToast(data.message); 
                row.remove(); // Remove row from table immediately
            })
            .catch(err => {
                console.error(err);
                alert('Failed to delete product.');
            });
        }

        // --- GLOBAL: CLOSE MODALS ---
        if (target.matches('[data-close-modal]') || target.classList.contains('modal-overlay')) {
            const modal = target.closest('.modal-overlay') || target;
            toggleModal(modal, false);
        }
    });

    // ============================================================
    //  FORM SUBMISSION HANDLER (Generic)
    // ============================================================
    async function handleFormSubmit(e, form, modal) {
        e.preventDefault();
        const formData = new FormData(form);
        const url = form.dataset.url;
        
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn ? btn.innerText : '';
        if(btn) { btn.disabled = true; btn.innerText = 'Saving...'; }

        try {
            const response = await fetch(url, {
                method: 'POST', // Always POST (Laravel handles the rest)
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (response.ok) {
                showToast(result.message);
                if(modal) toggleModal(modal, false);
                // Reload to see updated table/images
                setTimeout(() => window.location.reload(), 500);
            } else {
                let msg = result.message || 'Error processing request.';
                if(result.errors) {
                    msg += '\n' + Object.values(result.errors).flat().join('\n');
                }
                alert(msg);
            }
        } catch(error) { 
            console.error(error); 
            alert('Network Error occurred.'); 
        } finally {
            if(btn) { btn.disabled = false; btn.innerText = originalText; }
        }
    }

    // Attach listener to Product Form
    if (productForm) {
        productForm.addEventListener('submit', (e) => handleFormSubmit(e, productForm, productModal));
    }

    // ============================================================
    //  IMAGE PREVIEW HANDLER
    // ============================================================
    const imageInput = document.getElementById('p_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const preview = document.getElementById('image-preview');
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // ... (Retain existing Logout and other logic from your original file) ...
});