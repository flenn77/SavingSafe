import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import '@symfony/ux-dropzone';
import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
)); 


document.addEventListener('DOMContentLoaded', (event) => {
    const dateColumn = document.querySelector("#dateAdded");
    const sizeColumn = document.querySelector("#sizeFileColumn");
    let isSortedAsc = { date: false, size: false };

    let dateColumnIndex = isAdmin ? 4 : 5;
    let sizeColumnIndex = isAdmin ? 3 : 4;

    console.log(isAdmin)

    function sortTable(column, columnIndex, isNumeric = false) {
        const table = document.querySelector("table tbody");
        const rows = Array.from(table.querySelectorAll("tr"));
        
        rows.sort((a, b) => {
            const cellA = a.querySelector(`td:nth-child(${columnIndex})`).innerText;
            const cellB = b.querySelector(`td:nth-child(${columnIndex})`).innerText;

            const valueA = isNumeric ? parseInt(cellA, 10) : new Date(cellA);
            const valueB = isNumeric ? parseInt(cellB, 10) : new Date(cellB);

            return isSortedAsc[column] ? valueA - valueB : valueB - valueA;
        });

        isSortedAsc[column] = !isSortedAsc[column];

        while (table.firstChild) {
            table.removeChild(table.firstChild);
        }

        rows.forEach(row => table.appendChild(row));
    }

    dateColumn.addEventListener("click", () => sortTable('date', dateColumnIndex));
    sizeColumn.addEventListener("click", () => sortTable('size', sizeColumnIndex, true));
});



document.getElementById('fileSearchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('table tbody tr');

    tableRows.forEach(row => {
        const fileName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        if (fileName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});


