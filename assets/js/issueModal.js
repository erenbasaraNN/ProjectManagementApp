export function initializeIssueModal() {
    const modal = document.getElementById('issueModal');
    const closeBtn = modal.querySelector('.close');
    const saveBtn = document.getElementById('saveDescription');
    const descriptionTextarea = document.getElementById('issueDescription');
    let currentIssueId = null;

    // Open modal
    document.querySelectorAll('.open-updates-modal-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentIssueId = this.getAttribute('data-id');
            fetchDescription(currentIssueId).then(description => {
                descriptionTextarea.value = description;
                modal.style.display = 'block';
            });
        });
    });

    // Close modal
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }

    // Save description
    saveBtn.onclick = function() {
        if (currentIssueId) {
            saveDescription(currentIssueId, descriptionTextarea.value);
        }
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
}

function fetchDescription(issueId) {
    return fetch(`/api/issue/${issueId}/description`)
        .then(response => response.json())
        .then(data => data.description || '');
}

function saveDescription(issueId, description) {
    fetch(`/api/issue/${issueId}/description`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description }),
    }).then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Description saved successfully');
                document.getElementById('issueModal').style.display = 'none';
            } else {
                alert('Failed to save description: ' + (data.message || 'Unknown error'));
            }
        });
}