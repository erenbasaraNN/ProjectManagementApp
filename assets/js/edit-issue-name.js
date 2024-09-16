export default function handleIssueNameEdit(element) {
    const elements = element ? [element] : document.querySelectorAll('.issue-name');
    elements.forEach(function(element) {
        if (!element.classList.contains('issue-name-initialized')) {
            element.addEventListener('blur', function() {
                const issueId = this.dataset.id;
                const newName = this.innerText;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/issue/${issueId}/edit-name`, {
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
                    });
            });
            element.classList.add('issue-name-initialized');
        }
    });
}