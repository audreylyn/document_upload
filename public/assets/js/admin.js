// admin.js

const modal = document.getElementById('applicationModal');
const closeBtn = document.getElementsByClassName('close')[0];
const applicationDetails = document.getElementById('applicationDetails');

closeBtn.onclick = function() {
    modal.style.display = "none";
};

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

function switchTab(tabName) {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => tab.classList.remove('active'));
    tabContents.forEach(content => content.style.display = 'none');

    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.querySelector(`#${tabName}`).style.display = 'block';
}

// Preview functionality
function showPreview(path, type) {
    if (!path) return;

    // Remove any existing preview containers first
    document.querySelectorAll('.preview-popover').forEach(preview => preview.remove());

    // Create and style the preview container
    const previewContainer = document.createElement('div');
    previewContainer.className = 'preview-popover';
    
    // Add inline styles for better preview display
    previewContainer.style.position = 'fixed';
    previewContainer.style.top = '50%';
    previewContainer.style.left = '50%';
    previewContainer.style.transform = 'translate(-50%, -50%)';
    previewContainer.style.zIndex = '9999';
    previewContainer.style.width = '80%';
    previewContainer.style.maxWidth = '800px';
    previewContainer.style.height = '80%';
    previewContainer.style.maxHeight = '600px';
    previewContainer.style.backgroundColor = '#fff';
    previewContainer.style.boxShadow = '0 0 20px rgba(0,0,0,0.3)';
    previewContainer.style.borderRadius = '8px';
    previewContainer.style.overflow = 'hidden';
    previewContainer.style.display = 'flex';
    previewContainer.style.flexDirection = 'column';
    
    // Add styles for preview actions and buttons
    const style = document.createElement('style');
    style.textContent = `
        .preview-content {
            flex: 1;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-content img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .preview-actions {
            display: flex;
            justify-content: center;
            padding: 10px;
            gap: 10px;
            border-top: 1px solid #eee;
        }
        .preview-actions button {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            background: #4a6cf7;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        .preview-actions button:hover {
            background: #3a5bf0;
        }
    `;
    document.head.appendChild(style);
    
    // Remove the style element when the preview is closed
    const removeStyle = () => {
        if (document.head.contains(style)) {
            document.head.removeChild(style);
        }
    };
    
    // Add event listener to remove style when preview is closed
    previewContainer.addEventListener('remove', removeStyle);
    
    let content = '';
    const fileExtension = path.split('.').pop().toLowerCase();
    
    // Ensure path is properly formatted for URL
    const encodedPath = path.split('/').map(part => encodeURIComponent(part)).join('/');
    
    // Use the same path format as the download link but with inline display parameter
    const fileUrl = `/admin/download/${encodedPath.replace('uploads/', '')}?toolbar=1`;
    
    // Create a separate URL for the download button (without the toolbar parameter)
    const downloadUrl = `/admin/download/${encodedPath.replace('uploads/', '')}`;
    
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        content = `
            <div class="preview-content">
                <img src="${fileUrl}" alt="Document preview" style="max-width: 100%; max-height: 100%; object-fit: contain;"
                    onerror="this.onerror=null; this.parentElement.innerHTML='<p>Error loading image</p>';">
            </div>
            <div class="preview-actions">
                <button onclick="window.open('${fileUrl}', '_blank')">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </button>
                <button onclick="window.open('${downloadUrl}', '_blank')">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        `;
    } else if (fileExtension === 'pdf') {
        // For PDFs, we need to use an object tag instead of iframe since we're using a download endpoint
        content = `
            <div class="preview-content">
                <object data="${fileUrl}#toolbar=0" type="application/pdf" width="100%" height="100%">
                    <p>PDF preview not available. <a href="${fileUrl}" target="_blank">Download</a> to view.</p>
                </object>
            </div>
            <div class="preview-actions">
                <button onclick="window.open('${fileUrl}', '_blank')">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </button>
                <button onclick="window.open('${downloadUrl}', '_blank')">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        `;
    } else {
        content = `
            <div class="preview-content">
                <p>Preview not available for this file type</p>
            </div>
            <div class="preview-actions">
                <button onclick="window.open('${fileUrl}', '_blank')">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </button>
                <button onclick="window.open('${downloadUrl}', '_blank')">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        `;
    }
    
    previewContainer.innerHTML = content;
    document.body.appendChild(previewContainer);

    // Add show class for animation
    setTimeout(() => previewContainer.classList.add('show'), 10);
    
    // Create close button
    const closeButton = document.createElement('button');
    closeButton.innerHTML = '&times;';
    closeButton.style.position = 'absolute';
    closeButton.style.top = '10px';
    closeButton.style.right = '10px';
    closeButton.style.background = 'transparent';
    closeButton.style.border = 'none';
    closeButton.style.fontSize = '24px';
    closeButton.style.cursor = 'pointer';
    closeButton.style.color = '#333';
    closeButton.style.zIndex = '10';
    closeButton.onclick = function() {
        previewContainer.remove();
    };
    
    previewContainer.appendChild(closeButton);
    
    // Prevent the click from immediately closing the preview
    previewContainer.onclick = function(e) {
        e.stopPropagation();
    };
    
    // Add event handling for closing when clicking outside
    setTimeout(() => {
        document.addEventListener('click', function closePreview(e) {
            if (!previewContainer.contains(e.target)) {
                previewContainer.remove();
                document.removeEventListener('click', closePreview);
            }
        });
    }, 100);
}

function viewApplication(email) {
    const modal = document.getElementById('applicationModal');
    const modalContent = document.getElementById('applicationDetails');
    const closeBtn = modal.querySelector('.close');

    // Show loading state
    modalContent.innerHTML = '<div class="loading">Loading...</div>';
    modal.style.display = 'block';

    // Fetch application details
    fetch(`${window.adminEndpoints.getApplication}/${encodeURIComponent(email)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const documentIcons = {
                    'form138': '<i class="fas fa-file-alt"></i>',
                    'good_moral': '<i class="fas fa-certificate"></i>',
                    'birth_certificate': '<i class="fas fa-file-medical"></i>',
                    'id_picture': '<i class="fas fa-id-card"></i>',
                    'medical_clearance': '<i class="fas fa-notes-medical"></i>',
                    'application_form': '<i class="fas fa-file-signature"></i>'
                };

                const documentTitles = {
                    'form138': 'Form 138',
                    'good_moral': 'Good Moral Certificate',
                    'birth_certificate': 'Birth Certificate',
                    'id_picture': 'ID Picture',
                    'medical_clearance': 'Medical Clearance',
                    'application_form': 'Application Form'
                };

                const documentDesc = {
                    'form138': 'Grade 12 reflecting 2nd grading period',
                    'good_moral': 'Certificate of Good Moral Character',
                    'birth_certificate': 'PSA Birth Certificate',
                    'id_picture': '2x2 ID Picture',
                    'medical_clearance': 'Medical Certificate from authorized physician',
                    'application_form': 'Completed application form'
                };

                let html = `
                    <div class="applicant-info">
                        <div class="info-group">
                            <label>Email:</label>
                            <span>${data.email}</span>
                        </div>
                        <div class="info-group">
                            <label>Uploaded:</label>
                            <span>${new Date(data.uploaded_at).toLocaleString()}</span>
                        </div>
                        <div class="info-group">
                            <label>Status:</label>
                            <span class="status status-${data.status}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>
                        </div>
                    </div>

                    <div class="tab-container">
                        <div class="tab active" data-status="all" onclick="filterDocuments('all')">All</div>
                        <div class="tab" data-status="pending" onclick="filterDocuments('pending')">
                            Pending (${data.document_counts.pending})
                        </div>
                        <div class="tab" data-status="approved" onclick="filterDocuments('approved')">
                            Approved (${data.document_counts.approved})
                        </div>
                        <div class="tab" data-status="declined" onclick="filterDocuments('declined')">
                            Declined (${data.document_counts.declined})
                        </div>
                    </div>

                    <div class="documents-section">
                        <div class="document-list">`;

                // Add all possible document types
                Object.keys(documentTitles).forEach(docType => {
                    const docData = data.documents[docType] || { status: 'pending', path: null, notes: null };
                    const filePath = docData.path ? docData.path : '';
                    
                    html += `
                        <div class="document-item ${docData.status}" data-status="${docData.status}">
                            <div class="document-header">
                                <div class="document-title-section">
                                    ${docData.path ? `<div class="document-icon" onclick="showPreview('${filePath}', '${docType}')">${documentIcons[docType]}</div>` 
                                    : `<div class="document-icon no-preview">${documentIcons[docType]}</div>`}
                                    <div class="document-details">
                                        <h4>${documentTitles[docType]}</h4>
                                        <p>${documentDesc[docType]}</p>
                                    </div>
                                </div>
                                <div class="document-status-section">
                                    <div class="status-badge ${docData.status}">
                                        ${docData.status.charAt(0).toUpperCase() + docData.status.slice(1)}
                                    </div>
                                    ${docData.path ? `
                                        <a href="/admin/download/${encodeURIComponent(filePath.replace('uploads/', ''))}" class="download-btn" target="_blank">
                                            <i class="fas fa-download"></i>
                                            Download
                                        </a>
                                    ` : '<span class="not-uploaded">Not uploaded</span>'}
                                </div>
                            </div>
                            ${docData.path && docData.status === 'pending' ? `
                                <div class="document-actions">
                                    <button class="action-btn approve" onclick="updateDocumentStatus('${email}', '${docType}', 'approved')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="action-btn reject" onclick="showRejectDialog('${email}', '${docType}')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            ` : ''}
                            ${(docData.status === 'rejected' || docData.status === 'declined') && docData.notes ? `
                                <div class="rejection-details">
                                    <h4>Rejection Details</h4>
                                    <div class="rejection-notes">${docData.notes}</div>
                                </div>
                            ` : ''}
                        </div>`;
                });

                html += `
                        </div>
                    </div>`;

                modalContent.innerHTML = html;
            } else {
                modalContent.innerHTML = '<div class="error">Error loading application details</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = '<div class="error">Error loading application details</div>';
        });

    // Close modal when clicking the close button
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

// Single updateDocumentStatus function that handles both approval and rejection
function updateDocumentStatus(email, docType, status, notes = '', rejectionReason = '') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const requestData = {
        id: email,
        document: docType,
        status: status,
        notes: notes || '',
        rejection_reason: rejectionReason || ''
    };
    
    fetch(window.adminEndpoints.updateDocumentStatus, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showSuccessAlert(status);
            
            // Refresh the document view
            viewApplication(email);

            // Update the table row if needed
            const row = document.querySelector(`tr[data-email="${email}"]`);
            if (row) {
                const statusCell = row.querySelector('[data-label="Status"] .status');
                if (statusCell && data.application_status) {
                    statusCell.textContent = data.application_status.charAt(0).toUpperCase() + data.application_status.slice(1);
                    statusCell.className = `status status-${data.application_status}`;
                }
            }
        } else {
            showErrorAlert(data.message || 'Error updating document status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('Error updating document status');
    });
}

function filterDocuments(status) {
    const tabs = document.querySelectorAll('.tab');
    const documents = document.querySelectorAll('.document-item');
    
    tabs.forEach(tab => {
        tab.classList.remove('active');
        if (tab.getAttribute('data-status') === status) {
            tab.classList.add('active');
        }
    });
    
    documents.forEach(doc => {
        const docStatus = doc.getAttribute('data-status');
        if (status === 'all' || 
            docStatus === status || 
            (status === 'rejected' && docStatus === 'declined') ||
            (status === 'declined' && docStatus === 'rejected')) {
            doc.style.display = 'flex';
        } else {
            doc.style.display = 'none';
        }
    });
}

function showRejectDialog(email, docType) {
    const rejectDialog = document.createElement('div');
    rejectDialog.className = 'reject-dialog';
    rejectDialog.innerHTML = `
        <div class="reject-content">
            <h3>Reject Document</h3>
            <div class="reject-reasons">
                <button class="reason-btn" data-reason="Blurry">Blurry</button>
                <button class="reason-btn" data-reason="Too dim">Too dim</button>
                <button class="reason-btn" data-reason="Incorrect information">Incorrect information</button>
            </div>
            <textarea class="reject-notes" placeholder="Additional notes (optional)"></textarea>
            <div class="dialog-buttons">
                <button class="cancel-btn">Cancel</button>
                <button class="submit-btn" disabled>Submit</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(rejectDialog);
    
    let selectedReasons = new Set();
    const reasonBtns = rejectDialog.querySelectorAll('.reason-btn');
    const submitBtn = rejectDialog.querySelector('.submit-btn');
    const notesField = rejectDialog.querySelector('.reject-notes');
    
    reasonBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('selected');
            const reason = btn.getAttribute('data-reason');
            if (selectedReasons.has(reason)) {
                selectedReasons.delete(reason);
            } else {
                selectedReasons.add(reason);
            }
            submitBtn.disabled = selectedReasons.size === 0;
        });
    });
    
    rejectDialog.querySelector('.cancel-btn').addEventListener('click', () => {
        document.body.removeChild(rejectDialog);
    });
    
    submitBtn.addEventListener('click', () => {
        const reasons = Array.from(selectedReasons).join(', ');
        updateDocumentStatus(email, docType, 'declined', notesField.value, reasons);
        document.body.removeChild(rejectDialog);
    });
}

function updateStatus(id, status) {
    const customAlert = document.createElement('div');
    customAlert.className = 'custom-alert-overlay';
    customAlert.innerHTML = `
        <div class="custom-alert-box">
            <div class="custom-alert-content">
                <h3>Confirm Action</h3>
                <p>Are you sure you want to ${status} this application?</p>
                <div class="custom-alert-buttons">
                    <button class="custom-alert-button cancel">Cancel</button>
                    <button class="custom-alert-button confirm ${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(customAlert);
    
    const style = document.createElement('style');
    style.textContent = `
        .custom-alert-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .custom-alert-box {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease-out;
        }
        .custom-alert-content { text-align: center; }
        .custom-alert-content h3 { margin: 0 0 12px; color: #333; font-size: 20px; }
        .custom-alert-content p { margin: 0 0 24px; color: #666; font-size: 16px; }
        .custom-alert-buttons { display: flex; gap: 12px; justify-content: center; }
        .custom-alert-button { padding: 10px 24px; border-radius: 8px; border: none; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s ease; }
        .custom-alert-button.cancel { background: #f3f4f6; color: #666; }
        .custom-alert-button.cancel:hover { background: #e5e7eb; }
        .custom-alert-button.confirm { color: white; }
        .custom-alert-button.confirm.approved { background: #34d399; }
        .custom-alert-button.confirm.approved:hover { background: #10b981; }
        .custom-alert-button.confirm.declined { background: #f87171; }
        .custom-alert-button.confirm.declined:hover { background: #ef4444; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    `;
    document.head.appendChild(style);
    
    const cancelButton = customAlert.querySelector('.cancel');
    const confirmButton = customAlert.querySelector('.confirm');
    
    cancelButton.onclick = () => {
        document.body.removeChild(customAlert);
        document.head.removeChild(style);
    };
    
    confirmButton.onclick = () => {
        fetch(window.adminEndpoints.updateStatus, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: id, status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.body.removeChild(customAlert);
                document.head.removeChild(style);
                
                // Show success message
                showSuccessAlert(status);
                
                // Update UI without reloading
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    const statusCell = row.querySelector('[data-label="Status"] .status');
                    if (statusCell) {
                        statusCell.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        statusCell.className = `status status-${status}`;
                    }
                }
            } else {
                showErrorAlert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorAlert('Error updating status');
        })
        .finally(() => {
            if(document.body.contains(customAlert)) {
                document.body.removeChild(customAlert);
            }
            if(document.head.contains(style)) {
                document.head.removeChild(style);
            }
        });
    };
}

function showSuccessAlert(status) {
    const successAlert = document.createElement('div');
    successAlert.className = 'custom-alert success';
    successAlert.innerHTML = `<div class="alert-text ${status}">Document has been ${status} successfully!</div>`;
    document.body.appendChild(successAlert);

    const style = document.createElement('style');
    style.textContent = `
        .custom-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 500;
            min-width: 300px;
            max-width: 500px;
            animation: slideDown 0.3s ease-out, fadeOut 0.5s ease-in 3s forwards;
        }
        .custom-alert.success { background: white; }
        .alert-text { width: 100%; text-align: center; }
        .alert-text.approved { color: #10b981; }
        .alert-text.declined { color: #ef4444; }
        @keyframes slideDown { from { transform: translate(-50%, -100%); opacity: 0; } to { transform: translate(-50%, 0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    `;
    document.head.appendChild(style);
    
    setTimeout(() => {
        successAlert.remove();
        style.remove();
    }, 3500);
}

function showErrorAlert(message) {
    const errorAlert = document.createElement('div');
    errorAlert.className = 'custom-alert error';
    errorAlert.innerHTML = `
        <span class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg>
        </span>
        ${message}
    `;
    document.body.appendChild(errorAlert);
    
    setTimeout(() => {
        errorAlert.remove();
    }, 4000);
}

function filterApplications(status) {
    const table = $('#applicationsTable').DataTable();
    const tabs = document.querySelectorAll('.filter-tab');
    const dropdown = document.querySelector('.filter-dropdown select');

    tabs.forEach(tab => {
        tab.classList.remove('active');
        if (tab.textContent.toLowerCase().includes(status) ||
            (status === 'all' && tab.textContent.includes('All Applications'))) {
            tab.classList.add('active');
        }
    });
    dropdown.value = status;

    if (status === 'all') {
        table.column(2).search('').draw();
    } else {
        table.column(2).search(status, true, false).draw();
    }
}

$(document).ready(function() {
    $('#applicationsTable').DataTable({
        pageLength: 5,
        lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
        order: [[1, 'desc']],
        columnDefs: [{
            orderable: false,
            targets: 3
        }],
        language: {
            search: "Search applicant:",
            lengthMenu: "Show _MENU_ applications per page",
            info: "Showing _START_ to _END_ of _TOTAL_ applications",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});

// Global click listener to close any open preview popovers when clicking outside
document.addEventListener('click', function(e) {
    // Only close previews if the click is not on or inside a preview-popover
    // and not on an element with the document-icon class (which opens previews)
    const isClickOnPreview = e.target.closest('.preview-popover');
    const isClickOnDocIcon = e.target.closest('.document-icon');
    
    if (!isClickOnPreview && !isClickOnDocIcon) {
        document.querySelectorAll('.preview-popover').forEach(preview => preview.remove());
    }
});
