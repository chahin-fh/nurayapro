document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    const titleInput = document.getElementById('title');
    const priceInput = document.getElementById('price');
    const quantityInput = document.getElementById('quantity');
    const categoryInput = document.getElementById('category');
    const descriptionInput = document.getElementById('description');
    const photoInput = document.getElementById('photos');
    const photoUploadArea = document.getElementById('photoUploadArea');
    const errorImg = document.getElementById('errimg');

    // File input handling
    photoUploadArea.addEventListener('click', function() {
        photoInput.click();
    });

    // Handle drag and drop
    photoUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        photoUploadArea.classList.add('dragover');
    });

    photoUploadArea.addEventListener('dragleave', function() {
        photoUploadArea.classList.remove('dragover');
    });

    photoUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        photoUploadArea.classList.remove('dragover');
        photoInput.files = e.dataTransfer.files;
        handleFiles(e.dataTransfer.files);
    });

    photoInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (files.length === 0) {
            if (errorImg) {
                errorImg.textContent = 'Please select at least one photo';
                errorImg.style.color = 'var(--error-color)';
            }
            photoUploadArea.classList.remove('success');
            return;
        }

        // Validate file types and sizes
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        for (let file of files) {
            if (!validTypes.includes(file.type)) {
                if (errorImg) {
                    errorImg.textContent = 'Invalid file type. Please upload JPG, PNG, or GIF files only.';
                    errorImg.style.color = 'var(--error-color)';
                }
                photoUploadArea.classList.remove('success');
                return;
            }
            if (file.size > maxSize) {
                if (errorImg) {
                    errorImg.textContent = 'File size too large. Maximum size is 5MB.';
                    errorImg.style.color = 'var(--error-color)';
                }
                photoUploadArea.classList.remove('success');
                return;
            }
        }

        // If all validations pass
        if (errorImg) {
            errorImg.textContent = `${files.length} file(s) selected`;
            errorImg.style.color = 'var(--success-color)';
        }
        photoUploadArea.classList.add('success');
    }

    // Create error message elements
    const createErrorElement = (input) => {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentNode.appendChild(errorDiv);
        return errorDiv;
    };

    const titleError = createErrorElement(titleInput);
    const priceError = createErrorElement(priceInput);
    const quantityError = createErrorElement(quantityInput);
    const categoryError = createErrorElement(categoryInput);
    const descriptionError = createErrorElement(descriptionInput);

    // Add CSS for error messages
    const style = document.createElement('style');
    style.textContent = `
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
        input.error, textarea.error {
            border-color: #dc3545 !important;
        }
    `;
    document.head.appendChild(style);

    // Validation functions
    const validateTitle = () => {
        if (titleInput.value.trim().length < 3) {
            titleError.textContent = 'Title must be at least 3 characters long';
            titleError.style.display = 'block';
            titleInput.classList.add('error');
            return false;
        }
        titleError.style.display = 'none';
        titleInput.classList.remove('error');
        return true;
    };

    const validatePrice = () => {
        if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
            priceError.textContent = 'Please enter a valid price greater than 0';
            priceError.style.display = 'block';
            priceInput.classList.add('error');
            return false;
        }
        priceError.style.display = 'none';
        priceInput.classList.remove('error');
        return true;
    };

    const validateQuantity = () => {
        if (!quantityInput.value || parseInt(quantityInput.value) < 0) {
            quantityError.textContent = 'Please enter a valid quantity (0 or greater)';
            quantityError.style.display = 'block';
            quantityInput.classList.add('error');
            return false;
        }
        quantityError.style.display = 'none';
        quantityInput.classList.remove('error');
        return true;
    };

    const validateCategory = () => {
        if (!categoryInput.value || categoryInput.value === "0") {
            categoryError.textContent = 'Please select a valid category';
            categoryError.style.display = 'block';
            categoryInput.classList.add('error');
            return false;
        }
        categoryError.style.display = 'none';
        categoryInput.classList.remove('error');
        return true;
    };

    const validateDescription = () => {
        if (descriptionInput.value.trim().length < 10) {
            descriptionError.textContent = 'Description must be at least 10 characters long';
            descriptionError.style.display = 'block';
            descriptionInput.classList.add('error');
            return false;
        }
        descriptionError.style.display = 'none';
        descriptionInput.classList.remove('error');
        return true;
    };

    const validatePhoto = () => {
        if (!photoInput.files || photoInput.files.length === 0) {
            errorImg.textContent = 'Please select at least one photo';
            errorImg.style.color = 'var(--error-color)';
            photoUploadArea.classList.remove('success');
            return false;
        }
        return true;
    };

    // Add event listeners for real-time validation
    titleInput.addEventListener('input', validateTitle);
    priceInput.addEventListener('input', validatePrice);
    quantityInput.addEventListener('input', validateQuantity);
    categoryInput.addEventListener('change', validateCategory);
    descriptionInput.addEventListener('input', validateDescription);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const isTitleValid = validateTitle();
        const isPriceValid = validatePrice();
        const isQuantityValid = validateQuantity();
        const isCategoryValid = validateCategory();
        const isDescriptionValid = validateDescription();
        const isPhotoValid = validatePhoto();

        if (isTitleValid && isPriceValid && isQuantityValid && isCategoryValid && isDescriptionValid && isPhotoValid) {
            const submitBtn = this.querySelector('.btn');
            submitBtn.classList.add('loading');
            this.submit();
        }
    });
});
