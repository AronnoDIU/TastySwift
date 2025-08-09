/**
 * Admin Panel JavaScript
 * Handles interactive elements, forms, and UI components
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Handle file input preview
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Choose file';
            const label = this.nextElementSibling;
            label.textContent = fileName;
            
            // Preview image if it's an image file
            if (this.files && this.files[0] && this.files[0].type.startsWith('image/')) {
                const preview = this.closest('.file-upload-wrapper').querySelector('.image-preview');
                if (preview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail mt-2" style="max-height: 150px;">`;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });

    // Handle form submissions with loading state
    const forms = document.querySelectorAll('form[data-ajax-form]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            }
        });
    });

    // Initialize DataTables if available
    if (typeof $.fn.DataTable === 'function') {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries found",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });
    }

    // Handle sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
        });
    }

    // Handle dropdown submenus
    const dropdowns = document.querySelectorAll('.dropdown-menu a.dropdown-toggle');
    function handleDropdownClick(e) {
        const elm = this.nextElementSibling;
        if (!elm) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        // Close all other open submenus at this level
        const parentLi = this.parentElement;
        const parentUl = parentLi.parentElement;
        const openMenus = parentUl.querySelectorAll(':scope > li > .dropdown-menu.show');
        openMenus.forEach(menu => {
            if (menu !== elm) {
                menu.classList.remove('show');
            }
        });
        
        // Toggle this submenu
        elm.classList.toggle('show');
    }
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', handleDropdownClick);
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const openMenus = document.querySelectorAll('.dropdown-menu.show');
        openMenus.forEach(menu => {
            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    });
});

// Toast notifications
function showToast(type, message, title = '') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${title ? `<strong>${title}</strong><br>` : ''}
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
    toast.show();
    
    // Remove toast from DOM after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Global error handler for AJAX requests
$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
    let message = 'An error occurred while processing your request.';
    
    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
        message = jqXHR.responseJSON.message;
    } else if (jqXHR.status === 422 && jqXHR.responseJSON && jqXHR.responseJSON.errors) {
        // Handle validation errors
        const errors = [];
        for (const [key, value] of Object.entries(jqXHR.responseJSON.errors)) {
            errors.push(value[0]);
        }
        message = errors.join('<br>');
    } else if (jqXHR.status === 403) {
        message = 'You do not have permission to perform this action.';
    } else if (jqXHR.status === 401) {
        message = 'Your session has expired. Please refresh the page and try again.';
        // Optionally redirect to login page
        // window.location.href = '/admin/login';
    } else if (jqXHR.status === 404) {
        message = 'The requested resource was not found.';
    } else if (jqXHR.status >= 500) {
        message = 'A server error occurred. Please try again later.';
    }
    
    showToast('danger', message, 'Error');
});
