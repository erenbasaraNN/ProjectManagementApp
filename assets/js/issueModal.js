let currentIssueId = null; // Define at a broader scope

export function initializeIssueModal() {
    const modal = document.getElementById('issueModal');
    const closeBtn = modal.querySelector('.close');
    const saveBtn = document.getElementById('saveDescription');
    const addPostItBtn = document.getElementById('addPostItButton');
    const descriptionTextarea = document.getElementById('issueDescription');
    const postItSection = document.getElementById('post-it-section');
    const newPostItContent = document.getElementById('newPostItContent');

    function openModal(issueId) {
        console.log(`Opening modal for issueId: ${issueId}`);
        currentIssueId = issueId;
        fetchIssueData(issueId).then(issueData => {
            console.log('Fetched issue data:', issueData);
            descriptionTextarea.value = issueData.description;
            renderPostIts(issueData.postIts);
            modal.style.display = 'block';
        });
    }

    function closeModal() {
        console.log('Closing modal');
        modal.style.display = 'none';
    }

    function handleSaveDescription() {
        if (currentIssueId) {
            console.log('Saving description:', descriptionTextarea.value);
            saveDescription(currentIssueId, descriptionTextarea.value);
        }
    }

    function handleAddPostIt() {
        if (currentIssueId && newPostItContent.value.trim() !== '') {
            console.log('Adding Post-It:', newPostItContent.value.trim());
            addPostIt(currentIssueId, newPostItContent.value.trim()).then(() => {
                newPostItContent.value = ''; // Clear the textarea
                refreshPostIts(); // Ensure this function is defined and accessible
            });
        }
    }

    function handleClickOutside(event) {
        if (event.target === modal) {
            closeModal();
        }
    }

    function setupEventListeners() {
        document.querySelectorAll('.open-updates-modal-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                openModal(this.getAttribute('data-id'));
            });
        });

        closeBtn.onclick = closeModal;
        saveBtn.onclick = handleSaveDescription;
        addPostItBtn.onclick = handleAddPostIt;
        window.onclick = handleClickOutside;
    }

    setupEventListeners();
}

function refreshPostIts() {
    if (currentIssueId) {
        console.log('Refreshing Post-Its for issueId:', currentIssueId);
        fetchIssueData(currentIssueId).then(issueData => {
            console.log('Fetched issue data for refresh:', issueData);
            renderPostIts(issueData.postIts);
        });
    }
}

function fetchIssueData(issueId) {
    console.log('Fetching issue data from:', `/api/issue/${issueId}/data`);
    return fetch(`/api/issue/${issueId}/data`)
        .then(response => response.json())
        .then(data => {
            console.log('Fetched issue data:', data);
            return {
                description: data.description || '',
                postIts: data.postIts || []
            };
        })
        .catch(error => {
            console.error('Error fetching issue data:', error);
            return { description: '', postIts: [] }; // Fallback data
        });
}

function renderPostIts(postIts) {
    const postItSection = document.getElementById('post-it-section');
    postItSection.innerHTML = ''; // Clear previous content

    postIts.forEach(postIt => {
        const postItDiv = createPostItElement(postIt);
        postItSection.appendChild(postItDiv);
    });

    setupPostItEventListeners();
}

function createPostItElement(postIt) {
    const postItDiv = document.createElement('div');
    postItDiv.className = 'post-it bg-light p-3 my-3';
    postItDiv.dataset.id = postIt.id;
    postItDiv.innerHTML = `
        <p class="post-it-content">${postIt.content}</p>
        <small>Created by: ${postIt.createdBy} at ${new Date(postIt.createdAt).toLocaleString()}</small>
        <button class="btn btn-sm btn-secondary edit-postit-btn" data-id="${postIt.id}">Edit</button>
        <button class="btn btn-sm btn-danger delete-postit-btn" data-id="${postIt.id}">Delete</button>
    `;
    return postItDiv;
}

function setupPostItEventListeners() {
    console.log('Setting up event listeners for Post-Its');
    document.querySelectorAll('.edit-postit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postItId = this.getAttribute('data-id');
            console.log('Edit button clicked for Post-It:', postItId);
            // Handle editing of post-it (implement accordingly)
        });
    });

    document.querySelectorAll('.delete-postit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postItId = this.getAttribute('data-id');
            console.log('Delete button clicked for Post-It:', postItId);
            deletePostIt(postItId).then(() => {
                refreshPostIts(); // Refresh Post-Its after deletion
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
    console.log('Saving description to API:', description);
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
    console.log('Adding Post-It to API:', content);
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
    console.log('Deleting Post-It from API:', postItId);
    return fetch(`/postits/${postItId}/delete`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .catch(error => {
            console.error('Error deleting post-it:', error);
        });
}
