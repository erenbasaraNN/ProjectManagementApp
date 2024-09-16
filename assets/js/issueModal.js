export function initializeIssueModal() {
    const modal = document.getElementById('issueModal');
    const closeBtn = modal.querySelector('.close');
    const saveBtn = document.getElementById('saveDescription');
    const addPostItBtn = document.getElementById('addPostItButton');
    const descriptionTextarea = document.getElementById('issueDescription');
    const postItSection = document.getElementById('post-it-section');
    const newPostItContent = document.getElementById('newPostItContent');
    let currentIssueId = null;

    // Open modal
    document.querySelectorAll('.open-updates-modal-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentIssueId = this.getAttribute('data-id');
            fetchIssueData(currentIssueId).then(issueData => {
                descriptionTextarea.value = issueData.description;
                renderPostIts(issueData.postIts);
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

    // Add new Post-It
    addPostItBtn.onclick = function() {
        if (currentIssueId && newPostItContent.value.trim() !== '') {
            addPostIt(currentIssueId, newPostItContent.value.trim()).then(() => {
                newPostItContent.value = ''; // Clear the textarea
                fetchIssueData(currentIssueId).then(issueData => {
                    renderPostIts(issueData.postIts);
                });
            });
        }
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
}

function fetchIssueData(issueId) {
    return fetch(`/api/issue/${issueId}/data`)
        .then(response => response.json())
        .then(data => ({
            description: data.description || '',
            postIts: data.postIts || []
        }))
        .catch(error => {
            console.error('Error fetching issue data:', error);
            return { description: '', postIts: [] }; // Fallback data
        });
}

function renderPostIts(postIts) {
    const postItSection = document.getElementById('post-it-section');
    postItSection.innerHTML = ''; // Clear previous content

    postIts.forEach(postIt => {
        const postItDiv = document.createElement('div');
        postItDiv.className = 'post-it bg-light p-3 my-3';
        postItDiv.dataset.id = postIt.id;
        postItDiv.innerHTML = `
            <p class="post-it-content">${postIt.content}</p>
            <small>Created by: ${postIt.createdBy} at ${new Date(postIt.createdAt).toLocaleString()}</small>
            <button class="btn btn-sm btn-secondary edit-postit-btn" data-id="${postIt.id}">Edit</button>
            <button class="btn btn-sm btn-danger delete-postit-btn" data-id="${postIt.id}">Delete</button>
        `;
        postItSection.appendChild(postItDiv);
    });

    // Attach event listeners for edit and delete buttons
    document.querySelectorAll('.edit-postit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postItId = this.getAttribute('data-id');
            // Handle editing of post-it (implement accordingly)
        });
    });

    document.querySelectorAll('.delete-postit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postItId = this.getAttribute('data-id');
            deletePostIt(postItId).then(() => {
                fetchIssueData(currentIssueId).then(issueData => {
                    renderPostIts(issueData.postIts);
                });
            });
        });
    });
}

function fetchDescription(issueId) {
    return fetch(`/api/issue/${issueId}/description`)
        .then(response => response.json())
        .then(data => data.description || '');
}

function saveDescription(issueId, description) {
    return fetch(`/api/issue/${issueId}/description`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description }),
    })
        .then(response => response.json())
        .catch(error => {
            console.error('Error saving description:', error);
        });
}

function addPostIt(issueId, content) {
    return fetch(`/issues/${issueId}/postits`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ content }),
    })
        .then(response => response.json())
        .catch(error => {
            console.error('Error adding post-it:', error);
        });
}

function deletePostIt(postItId) {
    return fetch(`/postits/${postItId}/delete`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .catch(error => {
            console.error('Error deleting post-it:', error);
        });
}
