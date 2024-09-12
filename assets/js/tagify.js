// Function to update assignees for an issue
function updateIssueAssignees(issueId, assignees) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/issue/${issueId}/edit-assignees`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ assignees: assignees })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                console.log('Assignees updated successfully:', data.assignees);
            } else {
                console.error('Error updating assignees:', data.message);
            }
        })
        .catch(error => {
            console.error('Error updating assignees:', error);
        });
}

// Function to get the initials from a name
function getInitials(name) {
    return name.split(' ').map(part => part[0].toUpperCase()).join('');
}

// Initialize Tagify with user-specific colors and initials
export default function initializeTagify() {
    document.querySelectorAll('.tagify-input').forEach(function(input) {
        fetch('/users')  // Fetch the list of users from the server
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(function(whitelist) {
                const tagify = new Tagify(input, {
                    whitelist: whitelist.map(user => ({
                        value: user.name,
                        color: user.color // Use the user’s predefined color
                    })),
                    dropdown: {
                        position: "text",
                        enabled: 0
                    },
                    // Customize the tag template to display initials with color
                    templates: {
                        tag: function(tagData) {
                            const initials = getInitials(tagData.value);
                            const color = tagData.color || '#000000'; // Fallback to black if no color is provided
                            return `
                                <tag title="${tagData.value}" contenteditable="false" spellcheck="false" tabIndex="-1" class="tagify__tag tagify__tag--circle" style="--tag-bg:${color};">
                                    <span class="tagify__tag-text" style="color: white;">
                                        ${initials}
                                    </span>
                                </tag>
                            `;
                        }
                    }
                });

                // Handle changes to update assignees
                tagify.on('change', function(e) {
                    const issueId = input.dataset.id;
                    const assignees = e.detail.tagify.value.map(tag => tag.value);
                    updateIssueAssignees(issueId, assignees);
                });
            })
            .catch(error => {
                console.error('Error initializing Tagify:', error);
                // Fallback to initialize Tagify without the whitelist
                new Tagify(input, {
                    dropdown: {
                        position: "text",
                        enabled: 0
                    }
                });
            });
    });
}
