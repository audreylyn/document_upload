document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('upload-form');
    const filesList = document.getElementById('files-list');
    const emailInput = document.getElementById('applicant_email');

    // Track uploaded files (for UI tracking only)
    const uploadedFiles = new Map();

    // Handle file input changes
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            handleFileUpload(this);
        });
    });

    // Reset upload function: clears status and unhides the file input
    window.resetUpload = function(deleteButton) {
        const card = deleteButton.closest('.card');
        const cardBody = card.querySelector('.card-body');
        const fileInput = card.querySelector('input[type="file"]');
        const cardStatus = cardBody.querySelector('.upload-status');
        
        // Remove from tracking
        uploadedFiles.delete(fileInput.id);

        // Clear the file input value and remove the hidden flag so it is visible again
        fileInput.value = '';
        fileInput.classList.remove('hidden');

        // Remove the status message and hidden doc_type input (if present)
        if (cardStatus) {
            cardStatus.remove();
        }
        const hiddenDocType = card.querySelector('input[name="doc_type"]');
        if (hiddenDocType) {
            hiddenDocType.remove();
        }
    };

    // Define a mapping from displayed title to expected doc_type value
const docTypeMapping = {
    "ID Picture": "id_picture",
    "Form 138": "form138",
    "Good Moral Certificate": "good_moral",
    "Birth Certificate": "birth_certificate",
    "Medical Clearance": "medical_clearance",
    "Application Form": "application_form"
  };
  
  let docTitle = cardHeader.querySelector('.card-title').textContent.trim();
  hiddenDocType.value = docTypeMapping[docTitle] || docTitle.toLowerCase().replace(/\s+/g, '_');
  

    // Handle file upload function
    function handleFileUpload(input) {
        const card = input.closest('.card');
        const cardBody = card.querySelector('.card-body');
        const cardHeader = card.querySelector('.card-header');
        const file = input.files[0];

        if (file) {
            // Check file size
            if (file.size > 4 * 1024 * 1024) { // 4MB
                input.value = '';
                showTooltip(card, 'File size must be less than 4MB');
                return;
            }

            // Add to tracking (for UI purposes)
            uploadedFiles.set(input.id, file);

            // Create or get a status container in the card body
            let statusDiv = cardBody.querySelector('.upload-status');
            if (!statusDiv) {
                statusDiv = document.createElement('div');
                statusDiv.className = 'upload-status';
                cardBody.appendChild(statusDiv);
            }
            
            // Start showing upload progress in the status container
            statusDiv.innerHTML = `
                <div class="uploading-text">Uploading... <span class="percentage">0%</span></div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            `;

            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);

                    // After a short delay, update the UI to show success
                    setTimeout(() => {
                        statusDiv.innerHTML = `
                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                ${file.name}
                            </div>
                            <button type="button" class="delete-button" onclick="resetUpload(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        `;

                        // Hide (but do not remove) the file input so that its FileList is preserved
                        input.classList.add('hidden');

                        // Append a hidden input for doc_type (if not already present)
                        let hiddenDocType = card.querySelector('input[name="doc_type"]');
                        if (!hiddenDocType) {
                            hiddenDocType = document.createElement('input');
                            hiddenDocType.type = 'hidden';
                            hiddenDocType.name = 'doc_type';
                            // Use the card title as the doc_type value
                            hiddenDocType.value = cardHeader.querySelector('.card-title').textContent.trim();
                            card.appendChild(hiddenDocType);
                        }
                    }, 500);
                }

                // Update progress in the status container
                const progressBar = statusDiv.querySelector('.progress-bar');
                const percentage = statusDiv.querySelector('.percentage');
                if (progressBar && percentage) {
                    progressBar.style.width = `${progress}%`;
                    percentage.textContent = `${Math.round(progress)}%`;
                }
            }, 200);
        }
    }

    // Handle form submission with no duplicate-submission delay for simplicity
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate email field
        const email = emailInput.value.trim();
        if (!email) {
            showAlert('Please enter your email address', 'error');
            emailInput.focus();
            return;
        }

        // Create FormData from the form (the file input and hidden doc_type are preserved)
        const formData = new FormData(form);

        // Debug: Log FormData entries to verify submitted values
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ', pair[1]);
        }

        // Check that all required file inputs have a file
        let missingFiles = [];
        document.querySelectorAll('input[type="file"][required]').forEach(input => {
            if (!input.files || input.files.length === 0) {
                const cardTitle = input.closest('.card').querySelector('.card-title').textContent;
                missingFiles.push(cardTitle);
            }
        });
        if (missingFiles.length > 0) {
            showAlert(`Please upload the following required documents: ${missingFiles.join(', ')}`, 'error');
            return;
        }

        // Send the form data to the server
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Server Validation Error:', err);
                    throw new Error(err.message || 'Upload failed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message || 'Files uploaded successfully', 'success');
                form.reset();

                // Reset all upload cards
                document.querySelectorAll('.card').forEach(card => {
                    const deleteButton = card.querySelector('.delete-button');
                    if (deleteButton) {
                        resetUpload(deleteButton);
                    }
                });
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        })
        .catch(error => {
            showAlert(error.message, 'error');
        });
    });
});
