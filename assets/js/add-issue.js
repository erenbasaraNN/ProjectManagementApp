import initializeTagify from "./tagify";
import initializeStatusDropdowns from "./status-dropdowns";
import initializePriorityDropdowns from "./priority-dropdowns";
import initializeDatePicker from "./datepicker";
import handleIssueNameEdit from "./edit-issue-name";

export default function initializeAddIssue() {
    document.querySelectorAll('.add-issue-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const tagId = link.dataset.tagId;
            const projectId = document.querySelector('meta[name="project-id"]').getAttribute('content');
            const csrfToken = document.querySelector('input[name="_token"]').value;

            fetch(`/project/${projectId}/add-issue`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: `tag_id=${encodeURIComponent(tagId)}&_token=${csrfToken}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Yeni issue'yu tabloya dinamik olarak ekleyin
                        const issueTableBody = link.closest('tbody');
                        const newIssueHTML = `
                        <tr>
                            <td><span class="issue-name" data-id="${data.id}" contenteditable="true">${data.name}</span></td>
                            <td><input class="tagify-input" name="assignees" data-id="${data.id}" value="" placeholder="Kişi seç..."></td>
                            <td>
                                <div class="dropdown-container">
                                    <select class="status-dropdown" data-id="${data.id}">
                                        <option value="To DO" selected class="status-not-started">Başlanacak</option>
                                    </select>
                                </div>
                            </td>
                            <td><input type="text" class="datepicker" data-id="${data.id}" value="${data.endDate}"></td>
                            <td>
                                <select class="priority-dropdown" data-id="${data.id}">
                                    <option value="Düşük" selected class="priority-low">Düşük</option>
                                </select>
                            </td>
                        </tr>`;
                        issueTableBody.insertAdjacentHTML('beforeend', newIssueHTML);

                        // Yeni issue'ya JS işlevselliğini uygula
                        initializeTagify();  // Tagify için çağır
                        initializeStatusDropdowns();  // Status dropdown için çağır
                        initializePriorityDropdowns();  // Priority dropdown için çağır
                        initializeDatePicker();  // Datepicker için çağır
                        handleIssueNameEdit();  // İsim düzenleme fonksiyonunu çağır

                        console.log('JS functions re-initialized for new issue');
                    }
                })
                .catch(error => console.error('Error adding issue:', error));
        });
    });
}
