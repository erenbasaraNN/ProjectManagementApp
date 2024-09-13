import initializeAddIssue from "./add-issue";
import initializeTagify from "./tagify";
import {initializeStatusDropdowns} from "./status-dropdowns";
import initializePriorityDropdowns from "./priority-dropdowns";
import initializeDatePicker from "./datepicker";
import handleIssueNameEdit from "./edit-issue-name";

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed.');

    // Initial page load: Initialize for the entire page
    initializeAll();

    // Add event listener for adding new issues
    initializeAddIssue();
});

// Function to initialize all JavaScript for the page's existing content
function initializeAll() {
    try {
        initializeTagify(document.querySelectorAll('.tagify-input'));
        console.log('tagify.js initialized');
    } catch (error) {
        console.error('Error initializing tagify:', error);
    }

    try {
        initializeStatusDropdowns(document.querySelectorAll('.status-dropdown'));
        console.log('status-dropdowns.js initialized');
    } catch (error) {
        console.error('Error initializing status-dropdowns:', error);
    }

    try {
        initializePriorityDropdowns(document.querySelectorAll('.priority-dropdown'));
        console.log('priority-dropdowns.js initialized');
    } catch (error) {
        console.error('Error initializing priority-dropdowns:', error);
    }

    try {
        initializeDatePicker(document.querySelectorAll('.datepicker'));
        console.log('datepicker.js initialized');
    } catch (error) {
        console.error('Error initializing datepicker:', error);
    }

    try {
        handleIssueNameEdit(document.querySelectorAll('.issue-name'));
        console.log('edit-issue-name.js initialized');
    } catch (error) {
        console.error('Error initializing edit-issue-name:', error);
    }
}
