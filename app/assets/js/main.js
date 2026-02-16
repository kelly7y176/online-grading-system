/**
 * Main JavaScript
 * CS425 Assignment Grading System - Starter Codebase
 * 
 * Client-side functionality.
 * TODO for students: Add form validation, AJAX submissions, better UX
 */

// API Helper
const API = {
    baseUrl: '/api',

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}/${endpoint}`;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            },
        };

        const response = await fetch(url, { ...defaultOptions, ...options });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'API request failed');
        }

        return data;
    },

    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body),
        });
    },

    put(endpoint, body) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body),
        });
    },

    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    },
};

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initAlerts();
    initForms();
    initFileUpload();
    initConfirmDialogs();
    initRubricForm();
});

// Auto-dismiss alerts
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

// Form validation
function initForms() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });
    });
}

// File upload handling
function initFileUpload() {
    const fileUploads = document.querySelectorAll('.file-upload');
    
    fileUploads.forEach(upload => {
        const input = upload.querySelector('input[type="file"]');
        const label = upload.querySelector('.file-label');
        
        upload.addEventListener('click', () => input.click());
        
        upload.addEventListener('dragover', (e) => {
            e.preventDefault();
            upload.classList.add('dragover');
        });
        
        upload.addEventListener('dragleave', () => {
            upload.classList.remove('dragover');
        });
        
        upload.addEventListener('drop', (e) => {
            e.preventDefault();
            upload.classList.remove('dragover');
            
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                updateFileLabel(input, label);
            }
        });
        
        input.addEventListener('change', () => {
            updateFileLabel(input, label);
        });
    });
}

function updateFileLabel(input, label) {
    if (input.files.length > 0) {
        const fileName = input.files[0].name;
        const fileSize = formatFileSize(input.files[0].size);
        label.textContent = `${fileName} (${fileSize})`;
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Confirm dialogs
function initConfirmDialogs() {
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

// Dynamic rubric form
function initRubricForm() {
    const addRubricBtn = document.getElementById('add-rubric');
    const rubricContainer = document.getElementById('rubric-container');
    
    if (addRubricBtn && rubricContainer) {
        let rubricCount = rubricContainer.children.length;
        
        addRubricBtn.addEventListener('click', function() {
            const rubricItem = createRubricItem(rubricCount);
            rubricContainer.appendChild(rubricItem);
            rubricCount++;
            updateTotalPoints();
        });
        
        rubricContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-rubric')) {
                e.target.closest('.rubric-item').remove();
                updateTotalPoints();
            }
        });
        
        rubricContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('rubric-points')) {
                updateTotalPoints();
            }
        });
    }
}

function createRubricItem(index) {
    const div = document.createElement('div');
    div.className = 'rubric-item card mb-2';
    div.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-between align-center mb-1">
                <strong>Criterion ${index + 1}</strong>
                <button type="button" class="btn btn-sm btn-danger remove-rubric">Remove</button>
            </div>
            <div class="form-group">
                <input type="text" name="rubric[${index}][name]" class="form-control" placeholder="Criterion name" required>
            </div>
            <div class="form-group">
                <textarea name="rubric[${index}][description]" class="form-control" placeholder="Description (optional)" rows="2"></textarea>
            </div>
            <div class="form-group">
                <input type="number" name="rubric[${index}][points]" class="form-control rubric-points" placeholder="Max points" min="0" required>
            </div>
        </div>
    `;
    return div;
}

function updateTotalPoints() {
    const pointsInputs = document.querySelectorAll('.rubric-points');
    let total = 0;
    
    pointsInputs.forEach(input => {
        total += parseInt(input.value) || 0;
    });
    
    const totalDisplay = document.getElementById('total-points');
    if (totalDisplay) {
        totalDisplay.textContent = total;
    }
}

// Notification helper
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Grade calculation helper
function calculateGrade() {
    const rubricGrades = document.querySelectorAll('.rubric-grade-input');
    let total = 0;
    
    rubricGrades.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    const finalGradeInput = document.getElementById('final-grade');
    if (finalGradeInput) {
        finalGradeInput.value = total;
    }
}

// Export for use in other scripts
window.API = API;
window.showNotification = showNotification;
window.calculateGrade = calculateGrade;
