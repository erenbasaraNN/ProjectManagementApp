export default function handleIssueNameEdit() {
    const urlPrefix = ''; // Add your URL prefix here if needed, e.g., '/app'

    document.querySelectorAll('.issue-name').forEach(function(element) {
        element.addEventListener('blur', function() {
            const issueId = this.dataset.id;
            const newName = this.innerText;

            // Get the CSRF token from the meta tag
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send AJAX request to update issue name in the database
            fetch(`${urlPrefix}/issue/${issueId}/edit-name`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ name: newName })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Name updated successfully');
                    } else {
                        console.error('Error:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error updating name:', error);
                    if (error.message.includes('HTTP error!')) {
                        console.error('Server returned an error status. Check your route and server logs.');
                    }
                });
        });
    });
}