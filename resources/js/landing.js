document.addEventListener('DOMContentLoaded', function() {

    // --- CSRF Token Setup ---
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // --- DOM Elements ---
    const loginBtn = document.getElementById('login-btn');
    const signupBtn = document.getElementById('signup-btn');
    const signupStoreBtn = document.getElementById('signup-store-btn');
    const adminLoginBtn = document.getElementById('admin-login-btn');
    const registerShopBtn = document.getElementById('register-shop-btn');
    const mobileRegisterBtn = document.getElementById('mobile-register-btn');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    const signinModal = document.getElementById('signin-modal');
    const signupModal = document.getElementById('signup-modal');
    const adminLoginModal = document.getElementById('admin-login-modal');
    const signupStoreModal = document.getElementById('signup-store-modal');
    
    const closeModalBtns = document.querySelectorAll('[data-close-modal]');
    
    // --- Forms ---
    const signinForm = document.getElementById('signin-form');
    const signupForm = document.getElementById('signup-form');
    const adminLoginForm = document.getElementById('admin-login-form');
    const storeSignupForm = document.getElementById('store-signup-form');

    const goToSignupLink = document.getElementById('go-to-signup');
    const goToSigninLink = document.getElementById('go-to-signin');
    const backToUserLoginLink = document.getElementById('back-to-user-login');
    const forgotPasswordLink = document.getElementById('forgot-password');

    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    // --- Utility Functions ---
    function showModal(modal) {
        if (modal) {
            hideAllModals();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function hideAllModals() {
        [signinModal, signupModal, adminLoginModal, signupStoreModal].forEach(modal => {
            if(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
        document.body.style.overflow = 'auto';
    }
    
    function showToast(message, type = 'success') {
        if (!toastMessage) return;
        toastMessage.textContent = message;
        
        if (type === 'error') {
            toast.classList.remove('bg-green-700');
            toast.classList.add('bg-red-600');
        } else {
            toast.classList.remove('bg-red-600');
            toast.classList.add('bg-green-700');
        }
        
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    // --- Modal Controls ---
    if (loginBtn) loginBtn.addEventListener('click', () => showModal(signinModal));
    if (signupBtn) signupBtn.addEventListener('click', () => showModal(signupModal));
    if (signupStoreBtn) signupStoreBtn.addEventListener('click', () => showModal(signupStoreModal));
    if (adminLoginBtn) adminLoginBtn.addEventListener('click', () => showModal(adminLoginModal));
    if (registerShopBtn) registerShopBtn.addEventListener('click', () => showModal(signupStoreModal));
    
    if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', () => {
        showModal(signupStoreModal);
        if (mobileMenu) mobileMenu.classList.add('hidden');
    });
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            if (mobileMenu) mobileMenu.classList.toggle('hidden');
        });
    }
    
    document.addEventListener('click', (e) => {
        if (mobileMenu && !mobileMenu.contains(e.target) && mobileMenuBtn && !mobileMenuBtn.contains(e.target)) {
            mobileMenu.classList.add('hidden');
        }
    });

    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            hideAllModals();
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            hideAllModals();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") {
            hideAllModals();
        }
    });

    // --- Form Switching ---
    if(goToSignupLink) goToSignupLink.addEventListener('click', (e) => {
        e.preventDefault();
        hideAllModals();
        showModal(signupModal);
    });

    if(goToSigninLink) goToSigninLink.addEventListener('click', (e) => {
        e.preventDefault();
        hideAllModals();
        showModal(signinModal);
    });
    
    if(backToUserLoginLink) backToUserLoginLink.addEventListener('click', (e) => {
         e.preventDefault();
        hideAllModals();
        showModal(signinModal);
    });
    
    if(forgotPasswordLink) forgotPasswordLink.addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = '/forgot-password';
    });

    // --- Helper function to handle responses (Redirect vs JSON) ---
    async function handleResponse(response, form, errorDiv) {
        const contentType = response.headers.get("content-type");
        
        // If the server sends back HTML (a redirect), follow it
        if (contentType && contentType.indexOf("text/html") !== -1) {
            if (response.redirected) {
                 window.location.href = response.url;
            } else {
                 window.location.reload();
            }
            return;
        }

        // Otherwise, parse JSON
        const data = await response.json();

        if (response.ok) {
            showToast(data.message || 'Success! Redirecting...');
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                // For shop registration, we might just want to close the modal and stay
                if (form.id === 'store-signup-form') {
                     form.reset();
                     const fileLabel = document.getElementById('file-label-text');
                     if(fileLabel) fileLabel.innerText = 'DTI/Business Permit (PDF, JPG, PNG)';
                     hideAllModals();
                } else {
                    window.location.reload();
                }
            }
        } else {
            if (data.errors) {
                 let errorMessages = '';
                 const errors = Array.isArray(data.errors) ? data.errors : Object.values(data.errors).flat();
                 errors.forEach(err => errorMessages += `<li>${err}</li>`);
                 errorDiv.innerHTML = `<ul>${errorMessages}</ul>`;
            } else {
                errorDiv.innerHTML = data.message || 'An error occurred.';
            }
            errorDiv.classList.remove('hidden');
        }
    }

    // --- Generic Form Handler ---
    async function submitFormWithToken(e, form, errorDivId) {
        e.preventDefault();
        const errorDiv = document.getElementById(errorDivId);
        if (errorDiv) {
            errorDiv.innerHTML = '';
            errorDiv.classList.add('hidden');
        }

        const formData = new FormData(form);
        // Manually add token to body as backup
        if (csrfToken) formData.append('_token', csrfToken); 

        try {
            // --- THIS IS THE FIX ---
            // We check for the attribute explicitly. If it's missing (like in Create Shop),
            // we use the data-action. This prevents using the default current page URL.
            const actionUrl = form.getAttribute('action') || form.dataset.action;
            
            console.log('Submitting to:', actionUrl); // Debugging line to see where it goes

            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
            });
            await handleResponse(response, form, errorDiv);
        } catch (error) {
            console.error(error);
            if (errorDiv) {
                errorDiv.innerHTML = 'Connection error. Please refresh and try again.';
                errorDiv.classList.remove('hidden');
            }
        }
    }

    // --- Attach Event Listeners to Forms ---
    if (signinForm) {
        signinForm.addEventListener('submit', (e) => submitFormWithToken(e, signinForm, 'signin-errors'));
    }

    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', (e) => submitFormWithToken(e, adminLoginForm, 'admin-errors'));
    }

    if (signupForm) {
        signupForm.addEventListener('submit', (e) => submitFormWithToken(e, signupForm, 'signup-errors'));
    }

    if (storeSignupForm) {
        storeSignupForm.addEventListener('submit', (e) => submitFormWithToken(e, storeSignupForm, 'store-signup-errors'));
    }

    // --- File Input Label ---
    const fileUpload = document.getElementById('file-upload');
    const fileLabel = document.getElementById('file-label-text');
    if (fileUpload && fileLabel) {
        const defaultLabelText = fileLabel.innerText;
        fileUpload.addEventListener('change', () => {
            if (fileUpload.files.length > 0) {
                fileLabel.innerText = fileUpload.files[0].name;
            } else {
                fileLabel.innerText = defaultLabelText;
            }
        });
    }

    // --- Smooth Scrolling ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                if (mobileMenu) mobileMenu.classList.add('hidden');
            }
        });
    });

    // --- Add to Cart Buttons ---
    document.querySelectorAll('.bg-green-700').forEach(button => {
        button.addEventListener('click', function() {
            if (this.closest('.modal-overlay')) return;
            showToast('Item added to cart!');
        });
    });

});