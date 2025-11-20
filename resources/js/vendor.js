document.addEventListener('DOMContentLoaded', function() {
    
    // --- CSRF Token Setup ---
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // --- Elements ---
    const productModal = document.getElementById('product-form-modal');
    const productForm = document.getElementById('product-form');
    const modalTitle = document.getElementById('product-modal-title');
    const saveBtn = document.getElementById('save-product-btn');
    
    // --- Toast Function ---
    const toast = document.getElementById('toast');
    function showToast(message) {
        if (toast) {
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    }

    // --- Helper: Show/Hide Modal ---
    function toggleModal(modal, show = true) {
        if(modal) modal.style.display = show ? 'flex' : 'none';
    }

    // --- Event Delegation for Buttons ---
    document.body.addEventListener('click', function(e) {
        const target = e.target;

        // 1. OPEN "ADD NEW" MODAL
        if (target.id === 'new-product-btn') {
            e.preventDefault();
            if(productForm) {
                productForm.reset();
                document.getElementById('product_id').value = ''; 
                const preview = document.getElementById('image-preview');
                preview.src = '';
                preview.style.display = 'none';
                
                modalTitle.textContent = 'Add New Product';
                saveBtn.textContent = 'Add Product';
                
                // Set form destination to the "Store" route
                // Use a data attribute or hardcode the known route
                productForm.dataset.url = '/vendor/products'; 
            }
            toggleModal(productModal, true);
        }

        // 2. OPEN "EDIT" MODAL
        if (target.closest('.edit-product-btn')) {
            const row = target.closest('tr');
            const data = row.dataset;

            // Populate form fields
            document.getElementById('product_id').value = data.id;
            document.getElementById('p_name').value = data.name;
            document.getElementById('p_description').value = data.description;
            document.getElementById('p_price').value = data.price;
            
            // Show existing image
            const preview = document.getElementById('image-preview');
            preview.src = data.imageUrl;
            preview.style.display = 'block';

            modalTitle.textContent = 'Edit Product';
            saveBtn.textContent = 'Update Product';
            
            // Set form destination to the specific "Update" route
            productForm.dataset.url = data.updateUrl;

            toggleModal(productModal, true);
        }

        // 3. CLOSE MODAL
        if (target.matches('[data-close-modal]') || target === productModal) {
            toggleModal(productModal, false);
        }

        // 4. DELETE PRODUCT
        if (target.closest('.remove-product-btn')) {
            if (!confirm('Are you sure you want to delete this product?')) return;

            const row = target.closest('tr');
            const deleteUrl = row.dataset.deleteUrl;

            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message || 'Product deleted');
                row.remove(); // Remove row from table immediately
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // --- FORM SUBMISSION (Create & Update) ---
    if (productForm) {
        productForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.dataset.url;
            
            // Disable button to prevent double submit
            saveBtn.disabled = true;
            saveBtn.innerText = 'Saving...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    showToast(result.message);
                    toggleModal(productModal, false);
                    // Reload page to see new data (simplest way)
                    window.location.reload(); 
                } else {
                    alert('Error: ' + (result.message || 'Something went wrong'));
                    console.error(result);
                }
            } catch (error) {
                console.error(error);
                alert('Network error occurred.');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Product';
            }
        });
    }

    // --- Image Preview Helper ---
    const imageInput = document.getElementById('p_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Logout Logic
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            // Since we don't have the complex admin logout modal here, 
            // we'll create a hidden form and submit it.
            if(confirm("Are you sure you want to log out?")) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;
                
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});