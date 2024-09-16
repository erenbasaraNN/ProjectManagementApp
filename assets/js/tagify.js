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
export default function initializeTagify(input) {
    const inputs = input ? [input] : document.querySelectorAll('.tagify-input');
    inputs.forEach(function(input) {
        if (!input.classList.contains('tagify-initialized')) {
            fetch('/users')
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
                            color: user.color
                        })),
                        dropdown: {
                            position: "text",
                            enabled: 0
                        },
                        templates: {
                            tag: function(tagData) {
                                const initials = getInitials(tagData.value);
                                const color = tagData.color || '#000000';
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

                    tagify.on('change', function(e) {
                        const issueId = input.dataset.id;
                        const assignees = e.detail.tagify.value.map(tag => tag.value);
                        updateIssueAssignees(issueId, assignees);
                    });

                    input.classList.add('tagify-initialized');
                })
                .catch(error => {
                    console.error('Error initializing Tagify:', error);
                    new Tagify(input, {
                        dropdown: {
                            position: "text",
                            enabled: 0
                        }
                    });
                });
        }
    });
}