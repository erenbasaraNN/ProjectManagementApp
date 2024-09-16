import initializeTagify from "./tagify";
import {initializeStatusDropdowns} from "./status-dropdowns";
import {initializePriorityDropdowns} from "./priority-dropdowns";
import initializeDatePicker from "./datepicker";
import handleIssueNameEdit from "./edit-issue-name";

export default function initializeAddIssue() {
    document.querySelectorAll('.add-issue-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const tagId = link.dataset.tagId;
            const projectId = document.querySelector('meta[name="project-id"]').getAttribute('content');
            const csrfToken = document.querySelector('input[name="_token"]').value;

            // AJAX call to add a new issue
            fetch(`/project/${projectId}/add-issue`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: `tag_id=${encodeURIComponent(tagId)}&_token=${csrfToken}`
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        const issueTableBody = link.closest('tbody');
                        // Dynamically add a new row for the new issue
                        const newIssueHTML = `
                        <tr>
                            <td>
                                <span class="issue-name" data-id="${data.id}" contenteditable="true">${data.name}</span>
                                <button class="open-updates-modal-btn" data-id="${data.id}">
                                    <i class="fa-regular fa-comment"></i>
                                </button>
                            </td>
                            <td>
                                <div class="user-list">
                                    <input class="tagify-input" name="assignees" data-id="${data.id}"
                                           value="" placeholder="Kişi seç...">
                                </div>
                            </td>
                            <td>
                                <div class="status-tagify-container">
                                    <input class="status-tagify" name="status" data-id="${data.id}" value="Başlanacak" placeholder="Durum seç...">
                                </div>
                            </td>
                            <td>
                                <input type="text" class="datepicker" data-id="${data.id}" value="">
                            </td>
                            <td>
                                <div class="priority-tagify-container">
                                    <input class="priority-tagify" name="priority" data-id="${data.id}" value="Düşük" placeholder="Öncelik seç...">
                                </div>
                            </td>
                            <td>
                                        <div class="archive-button-container">
                                            <form method="POST" class="archive-form" action="{{ path('archive_issue', {'id': issue.id}) }}">
                                                <button type="submit" id="archive-button" class="archive-button">Archive</button>
                                            </form>
                                        </div>
                            </td>
                        </tr>`;
                        // Insert the new issue row into the table
                        issueTableBody.insertAdjacentHTML('beforeend', newIssueHTML);

                        const newRow = issueTableBody.lastElementChild;

                        // Initialize JS functionalities for the new row
                        initializeTagify(newRow.querySelector('.tagify-input'));  // Tagify for assignees
                        initializeStatusDropdowns(newRow.querySelector('.status-tagify'));  // Tagify for status
                        initializePriorityDropdowns(newRow.querySelector('.priority-tagify'));  // Tagify for priority
                        initializeDatePicker(newRow.querySelector('.datepicker'));  // Datepicker for end date
                        handleIssueNameEdit(newRow.querySelector('.issue-name'));  // Contenteditable name editing

                        console.log('JS functions initialized for new issue');
                    }
                })
                .catch(error => {
                    console.error('Error adding issue:', error);
                    alert('An error occurred while adding the issue. Please try again.');
                });
        });
    });
}
