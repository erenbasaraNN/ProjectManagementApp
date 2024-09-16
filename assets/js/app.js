import initializeAddIssue from "./add-issue";
import initializeTagify from "./tagify";
import {initializeStatusDropdowns} from "./status-dropdowns";
import {initializePriorityDropdowns} from "./priority-dropdowns";
import initializeDatePicker from "./datepicker";
import handleIssueNameEdit from "./edit-issue-name";
import initializeGroupAdd from "./group-add";
import { initializeIssueModal } from './issueModal.js';
function initializeAll() {
    const functions = [
        { name: 'tagify', fn: initializeTagify },
        { name: 'status-dropdowns', fn: initializeStatusDropdowns },
        { name: 'priority-dropdowns', fn: initializePriorityDropdowns },
        { name: 'datepicker', fn: initializeDatePicker },
        { name: 'edit-issue-name', fn: handleIssueNameEdit },
        { name: 'group-add', fn: initializeGroupAdd },
        { name: 'issueModal', fn: initializeIssueModal }
    ];

    functions.forEach(({ name, fn }) => {
        try {
            fn();
            console.log(`${name} initialized`);
        } catch (error) {
            console.error(`Error initializing ${name}:`, error);
        }
    });
}
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed.');

    // Initial page load: Initialize for the entire page
    initializeAll();

    // Add event listener for adding new issues
    initializeAddIssue();
});

// Function to initialize all JavaScript for the page's existing content
