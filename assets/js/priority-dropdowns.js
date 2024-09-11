export default function initializePriorityDropdowns() {
    document.querySelectorAll('.priority-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const issueId = this.dataset.id;
            const newPriority = this.value;

            // Send AJAX request to update priority in the database
            fetch(`/issue/${issueId}/edit-priority`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ priority: newPriority })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Priority updated successfully');
                    }
                }).catch(error => console.error('Error updating priority:', error));
        });
    });
}
