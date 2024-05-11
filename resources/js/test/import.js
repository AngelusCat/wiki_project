let table = document.getElementById('table');

async function updateTableWithArticles() {
    let response = await fetch('/getTable');
    let tableText = await response.text();
    table.innerHTML = tableText;
    await new Promise(resolve => setTimeout(resolve, 20000));
    if (flag) {
        await updateTableWithArticles();
    }




}

import {flag} from "@/test/index";

if (flag) {
    updateTableWithArticles();
}
