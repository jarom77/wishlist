document.addEventListener('DOMContentLoaded', () => {
    // Function to create a new column
    window.addColumn = function (name) {
        const container = document.getElementById('table-container');

        // Create the new column structure
        const column = document.createElement('div');
        column.className = 'column';

        // Add column title
        const title = document.createElement('h2');
        title.textContent = name;
        column.appendChild(title);

        // Add sub-table
        const table = document.createElement('table');
        table.innerHTML = `
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>Link</th>
            </tr>
        `;
        column.appendChild(table);

        // Append the new column to the container
        container.appendChild(column);
    };
});

