document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('upload-form');
    const emailInput = document.getElementById('applicant_email');
    let isSubmitting = false; // Flag to prevent duplicate submissions

    // Track staged files (not uploaded yet)
    const stagedFiles = new Map();

    // Map of document types to their correct enum values
    const docTypeMap = {
        'ID Picture': 'id_picture',
        'Form 138': 'form138',
        'Good Moral Certificate': 'good_moral',
        'Birth Certificate': 'birth_certificate',
        'Medical Clearance': 'medical_clearance',
        'Application Form': 'application_form'
    };

    // Show custom alert function
    function showAlert(message, type = 'success') {
        // Remove any existing alerts
        const existingAlerts = document.querySelectorAll('.custom-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `custom-alert ${type}`;
        alertDiv.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                ${type === 'success' 
                    ? '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'
                    : type === 'info'
                    ? '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>'
                    : '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>'
                }
            </svg>
            ${message}
        `;
        document.body.appendChild(alertDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Handle file input changes - STAGE FILES ONLY (don't upload immediately)
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            handleFileStaging(this);
        });
    });

    // Stage file function - show preview but don't upload yet
    function handleFileStaging(input) {
        const card = input.closest('.card');
        const cardBody = card.querySelector('.card-body');
        const cardTitle = card.querySelector('.card-title').textContent;
        const file = input.files[0];

        if (!file) {
            // User cancelled - remove from staged files and restore upload button
            stagedFiles.delete(input.id);
            restoreUploadButton(card, input);
            return;
        }

        // Validate file size
        if (file.size > 4 * 1024 * 1024) { // 4MB
            input.value = ''; // Clear the selected file
            showAlert('File size must be less than 4MB', 'error');
            stagedFiles.delete(input.id);
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            input.value = '';
            showAlert('Please select a valid file type (JPG, PNG, or PDF)', 'error');
            stagedFiles.delete(input.id);
            return;
        }

        // Stage the file (store in memory, don't upload yet)
        stagedFiles.set(input.id, {
            file: file,
            docType: docTypeMap[cardTitle],
            cardTitle: cardTitle
        });

        // Update UI to show staged file
        showStagedFilePreview(cardBody, file, cardTitle);
        
        showAlert(`${cardTitle} staged for upload. Click "Submit" to upload all documents.`, 'info');
    }

    // Show staged file preview in the card
    function showStagedFilePreview(cardBody, file, cardTitle) {
        cardBody.innerHTML = `
            <div class="upload-success staged">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>${file.name}</span>
            </div>
        `;
    }

    // Restore upload button
    function restoreUploadButton(card, input) {
        const cardBody = card.querySelector('.card-body');
        cardBody.innerHTML = `
            <input type="file" id="${input.id}" name="${input.id}" accept="${input.accept}" class="hidden" required>
            <button type="button" class="upload-button" onclick="document.getElementById('${input.id}').click()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Click to upload
            </button>
            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
        `;

        // Re-attach event listener
        const newInput = cardBody.querySelector('input[type="file"]');
        newInput.addEventListener('change', function(e) {
            handleFileStaging(this);
        });
    }

    // Change file function (global function for the change button)
    window.changeFile = function(button) {
        const card = button.closest('.card');
        const input = card.querySelector('input[type="file"]');
        
        // Remove from staged files
        stagedFiles.delete(input.id);
        
        // Clear current selection and trigger file picker
        input.value = '';
        input.click();
    };

    // Handle re-upload for declined documents (individual upload, not staged)
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.reupload');
        if (button) {
            e.preventDefault();
            
            const card = button.closest('.card');
            const input = card.querySelector('input[type="file"]');
            const cardTitle = card.querySelector('.card-title').textContent;
            const docType = docTypeMap[cardTitle];
            const email = emailInput.value.trim();

            if (!docType || !email) {
                console.error('Re-upload failed: Missing docType or email.');
                showAlert('An error occurred. Please refresh the page and try again.', 'error');
                return;
            }
            
            console.log(`Attempting to re-upload document type: ${docType}`);
            showAlert(`Preparing to re-upload ${cardTitle}...`, 'info');

            // Clear the input and trigger file selection
            input.value = '';
            
            // Create a temporary event listener for re-upload (immediate upload)
            const reuploadHandler = function(e) {
                const file = input.files[0];
                if (!file) return;

                // Validate file
                if (file.size > 4 * 1024 * 1024) {
                    input.value = '';
                    showAlert('File size must be less than 4MB', 'error');
                    return;
                }

                // Show upload progress
                const cardBody = card.querySelector('.card-body');
                const originalContent = cardBody.innerHTML;
                
                cardBody.innerHTML = `
                    <div class="uploading-text">Uploading... <span class="percentage">0%</span></div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                `;

                // Simulate progress and upload
                simulateProgressAndUpload(cardBody, file, docType, email, originalContent);
                
                // Remove this temporary handler
                input.removeEventListener('change', reuploadHandler);
            };

            // Add temporary handler and trigger file picker
            input.addEventListener('change', reuploadHandler);
            input.click();
        }
    });

    // Simulate progress and upload for re-uploads
    function simulateProgressAndUpload(cardBody, file, docType, email, originalContent) {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);

                setTimeout(() => {
                    submitIndividualFile(file, docType, email)
                        .then(data => {
                            showAlert('Document re-uploaded successfully!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        })
                        .catch(error => {
                            console.error('Error during re-upload:', error);
                            showAlert(error.message || 'Re-upload failed', 'error');
                            cardBody.innerHTML = originalContent;
                        });
                }, 500);
            }

            const progressBar = cardBody.querySelector('.progress-bar');
            const percentage = cardBody.querySelector('.percentage');
            if (progressBar && percentage) {
                progressBar.style.width = `${progress}%`;
                percentage.textContent = `${Math.round(progress)}%`;
            }
        }, 100);
    }

    // Submit individual file (for re-uploads)
    function submitIndividualFile(file, docType, email) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('applicant_email', email);
            formData.append('docfilename', file);
            formData.append('doc_type', docType);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Upload failed');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    resolve(data);
                } else {
                    reject(new Error(data.message || 'Upload failed'));
                }
            })
            .catch(error => {
                reject(error);
            });
        });
    }

    // Handle main form submission - upload all staged files
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Prevent duplicate submissions
        if (isSubmitting) return;
        isSubmitting = true;

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="spinning" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 11-6.219-8.56"/>
                </svg>
                Uploading...
            `;
        }

        const email = emailInput.value.trim();
        if (!email) {
            showAlert('An error occurred with your session. Please refresh the page.', 'error');
            resetSubmitButton(submitButton);
            return;
        }

        // Check if we have staged files to upload
        if (stagedFiles.size === 0) {
            showAlert('Please select at least one document to upload.', 'error');
            resetSubmitButton(submitButton);
            return;
        }

        // Validate required files (only check inputs that don't have existing uploads)
        let missingFiles = [];
        document.querySelectorAll('input[type="file"][required]').forEach(input => {
            // Check if this input has an existing upload or staged file
            const hasExistingUpload = input.closest('.card').querySelector('.upload-success:not(.staged)');
            const hasStagedFile = stagedFiles.has(input.id);
            
            if (!hasExistingUpload && !hasStagedFile) {
                const cardTitle = input.closest('.card').querySelector('.card-title').textContent;
                missingFiles.push(cardTitle);
            }
        });

        if (missingFiles.length > 0) {
            showAlert(`Please upload the following required documents: ${missingFiles.join(', ')}`, 'error');
            resetSubmitButton(submitButton);
            return;
        }

        try {
            showAlert('Uploading documents, please wait...', 'info');

            // Upload all staged files
            const uploadPromises = Array.from(stagedFiles.entries()).map(([inputId, fileData]) => {
                return submitIndividualFile(fileData.file, fileData.docType, email);
            });

            await Promise.all(uploadPromises);
            
            showAlert('All documents uploaded successfully!', 'success');
            
            // Clear staged files
            stagedFiles.clear();
            
            // Reload page to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 2000);

        } catch (error) {
            console.error('Upload error:', error);
            showAlert(error.message || 'Some files failed to upload. Please try again.', 'error');
        } finally {
            resetSubmitButton(submitButton);
        }
    });

    // Reset submit button
    function resetSubmitButton(submitButton) {
        isSubmitting = false;
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = `
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 2L11 13"></path>
                    <path d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                </svg>
                Submit
            `;
        }
    }
});

