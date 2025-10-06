/**
 * PSM Animations JavaScript
 * Enhances user experience with smooth animations and transitions
 * Excludes sidebar animations as requested
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize animations
    initializeAnimations();
    
    // Add intersection observer for scroll animations
    setupScrollAnimations();
    
    // Enhanced button interactions
    setupButtonAnimations();
    
    // Table row animations
    setupTableAnimations();
    
    // Form animations
    setupFormAnimations();
    
    // Modal animations
    setupModalAnimations();
    
    // Page transition animations
    setupPageTransitions();
});

/**
 * Initialize basic animations
 */
function initializeAnimations() {
    // Add stagger animation to statistics cards
    const statsRow = document.querySelector('.row.g-4.mb-4');
    if (statsRow && !statsRow.classList.contains('animate-stagger')) {
        statsRow.classList.add('animate-stagger');
    }
    
    // Add animation classes to cards
    const cards = document.querySelectorAll('.card:not(.stat-card)');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Add animation to tables
    const tables = document.querySelectorAll('.table-enhanced');
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
        });
    });
}

/**
 * Setup scroll-triggered animations
 */
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements that should animate on scroll
    const elementsToAnimate = document.querySelectorAll('.card:not(.stat-card), .table-container, .alert');
    elementsToAnimate.forEach(el => {
        observer.observe(el);
    });
}

/**
 * Enhanced button animations
 */
function setupButtonAnimations() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        // Add ripple effect on click
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
        
        // Add loading state animation
        button.addEventListener('click', function() {
            if (this.classList.contains('btn-loading')) {
                return;
            }
            
            // Add loading state for form submissions
            if (this.type === 'submit' || this.classList.contains('btn-submit')) {
                this.classList.add('btn-loading');
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-repeat spin me-2"></i>Processing...';
                
                // Remove loading state after 3 seconds (adjust as needed)
                setTimeout(() => {
                    this.classList.remove('btn-loading');
                    this.innerHTML = originalText;
                }, 3000);
            }
        });
    });
    
    // Add CSS for ripple effect
    if (!document.querySelector('#ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            .btn { position: relative; overflow: hidden; }
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            @keyframes ripple-animation {
                to { transform: scale(4); opacity: 0; }
            }
            .spin { animation: spin 1s linear infinite; }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
}

/**
 * Table animations
 */
function setupTableAnimations() {
    const tables = document.querySelectorAll('.table-enhanced');
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                this.style.transition = 'all 0.3s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    });
}

/**
 * Form animations
 */
function setupFormAnimations() {
    const formControls = document.querySelectorAll('.form-control, .form-select');
    
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('form-group-focused');
        });
        
        control.addEventListener('blur', function() {
            this.parentElement.classList.remove('form-group-focused');
        });
        
        // Add floating label effect
        control.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    });
}

/**
 * Modal animations
 */
function setupModalAnimations() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const modalDialog = this.querySelector('.modal-dialog');
            modalDialog.style.animation = 'none';
            modalDialog.offsetHeight; // Trigger reflow
            modalDialog.style.animation = 'modalSlideIn 0.3s ease-out';
        });
        
        modal.addEventListener('hide.bs.modal', function() {
            const modalDialog = this.querySelector('.modal-dialog');
            modalDialog.style.animation = 'modalSlideOut 0.3s ease-in';
        });
    });
    
    // Add modal animation styles
    if (!document.querySelector('#modal-animations')) {
        const style = document.createElement('style');
        style.id = 'modal-animations';
        style.textContent = `
            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-50px) scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            @keyframes modalSlideOut {
                from {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
                to {
                    opacity: 0;
                    transform: translateY(-50px) scale(0.9);
                }
            }
        `;
        document.head.appendChild(style);
    }
}

/**
 * Page transition animations
 */
function setupPageTransitions() {
    // Animate page load
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease-in-out';
    
    window.addEventListener('load', function() {
        document.body.style.opacity = '1';
    });
    
    // Animate navigation links (excluding sidebar)
    const navLinks = document.querySelectorAll('a:not(#sidebar a):not(.sidebar a)');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Only animate internal links
            if (this.hostname === window.location.hostname && !this.hasAttribute('data-bs-toggle')) {
                e.preventDefault();
                
                // Fade out current content
                const mainContent = document.querySelector('#main-content');
                if (mainContent) {
                    mainContent.style.opacity = '0.5';
                    mainContent.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 300);
                } else {
                    window.location.href = this.href;
                }
            }
        });
    });
}

/**
 * Add loading animation to page
 */
function showPageLoader() {
    const loader = document.createElement('div');
    loader.id = 'page-loader';
    loader.innerHTML = `
        <div class="loader-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading...</p>
        </div>
    `;
    
    const loaderStyles = `
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease-in-out;
        }
        .loader-content {
            text-align: center;
        }
    `;
    
    if (!document.querySelector('#loader-styles')) {
        const style = document.createElement('style');
        style.id = 'loader-styles';
        style.textContent = loaderStyles;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(loader);
    
    return loader;
}

/**
 * Remove page loader
 */
function hidePageLoader() {
    const loader = document.querySelector('#page-loader');
    if (loader) {
        loader.style.animation = 'fadeOut 0.3s ease-in-out';
        setTimeout(() => {
            loader.remove();
        }, 300);
    }
}

/**
 * Utility function to add animation class with delay
 */
function animateElement(element, animationClass, delay = 0) {
    setTimeout(() => {
        element.classList.add(animationClass);
    }, delay);
}

/**
 * Utility function to animate multiple elements with stagger
 */
function animateElements(elements, animationClass, staggerDelay = 100) {
    elements.forEach((element, index) => {
        animateElement(element, animationClass, index * staggerDelay);
    });
}

// Export functions for external use
window.PSMAnimations = {
    showPageLoader,
    hidePageLoader,
    animateElement,
    animateElements
};
