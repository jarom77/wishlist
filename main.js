// Function to open the date picker modal
function openDatePicker(itemid,notes) {
    const modal = document.getElementById('datePickerModal');
    modal.style.display = 'flex'; // Show the modal
    const dateItemId = document.getElementById('dateItemId');
    dateItemId.value = itemid;
    
    if (notes) {
        const noteDiv = document.getElementById('multiple-notes');
        noteDiv.style.display = 'block';
        const noteText = document.getElementById('notes-text');
        noteText.innerText = notes;
    }
}

// Function to open the item modal
function openItemWindow(itemid, recurring) {
    const modal = document.getElementById('itemModal');
    modal.style.display = 'flex'; // Show the modal
    const desc = document.getElementById('item'+itemid).children[0].innerHTML;
    const linkElements = document.getElementById('item'+itemid).getElementsByTagName('a');
    link = '';
    if (linkElements.length > 0) link = linkElements[0].href;

    document.getElementById('editItemId').value = itemid;
    document.getElementById('desc').value = desc;
    document.getElementById('link').value = link;
    document.getElementById('recurring').checked = recurring;
}

