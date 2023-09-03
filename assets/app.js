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
    let isSortedAsc = false;
    document.querySelector("#dateAdded").addEventListener("click", function() {
        const table = document.querySelector("table tbody");
        const rows = Array.from(table.querySelectorAll("tr"));

        rows.sort((a, b) => {
            const dateA = new Date(a.querySelector("td:nth-child(4)").innerText);
            const dateB = new Date(b.querySelector("td:nth-child(4)").innerText);

            return isSortedAsc ? dateA - dateB : dateB - dateA;
        });

        isSortedAsc = !isSortedAsc;

        while (table.firstChild) {
            table.removeChild(table.firstChild);
        }

        rows.forEach(row => table.appendChild(row));
    });
});