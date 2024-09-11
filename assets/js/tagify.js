
const predefinedColors = ['#FF8A8A', '#D2E0FB', '#B1AFFF', '#D37676', '#F6995C'];

function getRandomPredefinedColor() {
    // Return a random color from the predefined list
    return predefinedColors[Math.floor(Math.random() * predefinedColors.length)];
}
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
                // You can update the UI here if needed
            } else {
                console.error('Error updating assignees:', data.message);
            }
        })
        .catch(error => {
            console.error('Error updating assignees:', error);
        });
}

export default function initializeTagify() {
    document.querySelectorAll('.tagify-input').forEach(function(input) {
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
                        color: getRandomPredefinedColor() // Assign a random predefined color to each tag
                    })),
                    dropdown: {
                        position: "text",
                        enabled: 0
                    }
                });
                tagify.on('add', function(e) {
                    const tagElm = e.detail.tag;

                    // Set the custom property `--tag-bg` to the random color
                    tagElm.style.setProperty('--tag-bg', e.detail.data.color || getRandomPredefinedColor());
                });
                // Add event listener for changes
                tagify.on('change', function(e) {
                    const issueId = input.dataset.id;
                    const assignees = e.detail.tagify.value.map(tag => tag.value);
                    updateIssueAssignees(issueId, assignees);
                });
            })
            .catch(error => {
                console.error('Error initializing Tagify:', error);
                // Fallback to initialize Tagify without whitelist
                new Tagify(input, {
                    dropdown: {
                        position: "text",
                        enabled: 0
                    }
                });
            });
    });
}