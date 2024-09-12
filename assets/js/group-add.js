export default function initializeGroupAdd() {
    const projectId = document.querySelector('meta[name="project-id"]').getAttribute('content');

    const addGroupBtn = document.getElementById('add-group-btn');
    const modal = document.getElementById('add-group-modal');
    const closeModal = document.querySelector('.close-modal');
    const form = document.getElementById('add-group-form');

    // Show the modal when clicking "Add Group"
    addGroupBtn.addEventListener('click', function () {
        modal.style.display = 'flex';
    });

    // Close the modal when clicking the "X"
    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Close the modal when clicking outside the modal content
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Handle form submission via AJAX
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const groupName = document.getElementById('groupName').value;
        const groupColor = document.getElementById('groupColor').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;  // Get the CSRF token

        fetch(`/project/${projectId}/add-group`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: `group_name=${encodeURIComponent(groupName)}&group_color=${encodeURIComponent(groupColor)}&_token=${csrfToken}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    // Close the modal after successful submission
                    modal.style.display = 'none';

                    // Add the new group dynamically to the UI
                    const issueGroupsContainer = document.querySelector('.issue-groups');
                    const newGroupHTML = `
                        <div class="group">
                            <div class="group-header" style="border-left: 20px solid ${data.color}; border-radius: 5px;">
                                <h3>${data.name}</h3>
                            </div>
                            <table class="issue-table">
                                <thead>
                                    <tr>
                                        <th>Görev</th>
                                        <th>Kişi</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th>Priority</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="add-task-row">
                                        <td colspan="6"><a href="#" class="add-task-link"><i class="fas fa-plus"></i> öğe ekle</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                    issueGroupsContainer.insertAdjacentHTML('beforeend', newGroupHTML);
                }
            })
            .catch(error => console.error('Error adding group:', error));
    });
}
