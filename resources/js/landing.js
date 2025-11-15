document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
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
            
            const signinForm = document.getElementById('signin-form');
            const signupForm = document.getElementById('signup-form');
            const adminLoginForm = document.getElementById('admin-login-form');
            const storeSignupForm = document.getElementById('store-signup-form');
            
            // === MODIFICATION START ===
            const loginAsVendorCheckbox = document.getElementById('login-as-vendor');
            // === MODIFICATION END ===

            const goToSignupLink = document.getElementById('go-to-signup');
            const goToSigninLink = document.getElementById('go-to-signin');
            const backToUserLoginLink = document.getElementById('back-to-user-login');
            const forgotPasswordLink = document.getElementById('forgot-password');

            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');

            // Utility Functions
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
                toastMessage.textContent = message;
                
                // Set color based on type
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

            // --- Event Listeners ---
            if (loginBtn) loginBtn.addEventListener('click', () => showModal(signinModal));
            if (signupBtn) signupBtn.addEventListener('click', () => showModal(signupModal));
            if (signupStoreBtn) signupStoreBtn.addEventListener('click', () => showModal(signupStoreModal));
            if (adminLoginBtn) adminLoginBtn.addEventListener('click', () => showModal(adminLoginModal));
            if (registerShopBtn) registerShopBtn.addEventListener('click', () => showModal(signupStoreModal));
            if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', () => {
                showModal(signupStoreModal);
                mobileMenu.classList.add('hidden');
            });
            
            // Mobile Menu Toggle
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (mobileMenu && !mobileMenu.contains(e.target) && mobileMenuBtn && !mobileMenuBtn.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });

            // --- Modal Controls ---
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
                showToast('Password reset instructions sent to your email!');
                hideAllModals();
            });

            // --- Form Submissions ---
            if(signinForm) signinForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // Check if the vendor checkbox is checked
                if (loginAsVendorCheckbox && loginAsVendorCheckbox.checked) {
                    // Log in as Vendor
                    showToast('Vendor login successful! Redirecting to your dashboard...');
                    setTimeout(() => {
                        // *** CORRECTED PATH ***
                        window.location.href = '/vendor/dashboard'; 
                        hideAllModals();
                    }, 1500);
                } else {
                    // === FIX 1: ADDED CUSTOMER REDIRECT ===
                    // Log in as Customer
                    showToast('Welcome back! You have successfully signed in.');
                    setTimeout(() => {
                        // *** CORRECTED PATH ***
                        window.location.href = '/customer/dashboard'; 
                        hideAllModals();
                    }, 1500);
                    // === END FIX ===
                }
            });
            
            if(signupForm) signupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                // === FIX 2: ADDED CUSTOMER REDIRECT ===
                showToast('Account created successfully! Welcome to Flora Fleur.');
                setTimeout(() => {
                    // *** CORRECTED PATH ***
                    window.location.href = '/customer/dashboard';
                    hideAllModals();
                }, 1500);
                // === END FIX ===
            });
            
            if(adminLoginForm) adminLoginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                showToast('Admin login successful! Redirecting to dashboard...');
                setTimeout(() => {
                    // *** CORRECTED PATH ***
                    window.location.href = '/admin/dashboard';
                    hideAllModals();
                }, 1500);
            });
            
            if(storeSignupForm) storeSignupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                showToast('Your shop registration has been submitted for review!');
                hideAllModals();
            });

            // File input label
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

            // Smooth scrolling for navigation links
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
                        
                        // Close mobile menu if open
                        if (mobileMenu) {
                           mobileMenu.classList.add('hidden');
                        }
                    }
                });
            });

            // Add to cart buttons
            document.querySelectorAll('.bg-green-700').forEach(button => {
                button.addEventListener('click', function() {
                    // Check if the click is inside a modal, if so, don't show toast
                    if (this.closest('.modal-overlay')) return;
                    showToast('Item added to cart!');
                });
            });
        });