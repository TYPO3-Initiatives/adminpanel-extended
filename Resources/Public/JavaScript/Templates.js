/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
const Templates = {};

Templates.initialize = function () {
    const templateLinks = document.querySelectorAll('[data-typo3-role=template-link]');
    for (let i = 0; i < templateLinks.length; i++) {
        templateLinks[i].addEventListener('click', Templates.getTemplateData);
    }
};
Templates.getTemplateData = function (event) {
    event.preventDefault();
    const uri = this.dataset.typo3AjaxUrl;
    const request = new XMLHttpRequest();
    request.open('GET', uri);
    request.send();

    /**
     * @param template
     * @returns {HTMLTableRowElement}
     */
    function createTableRow(template) {
        const tableRow = document.createElement('tr');
        const templateData = document.createElement('td');
        const pre = document.createElement('pre');
        pre.setAttribute('class', 'prettyprint lang-html linenums');
        const code = document.createElement('code');
        code.innerText = template;
        templateData.setAttribute('colspan', '5');

        pre.appendChild(code);
        templateData.appendChild(pre);
        tableRow.appendChild(templateData);
        return tableRow;
    };

    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const data = JSON.parse(this.responseText);

            const target = document.querySelector('[data-typo3-template-id=' + data.templateId + ']');
            const templateRow = createTableRow(data.template);
            templateRow.id = data.templateId;
            target.parentNode.parentNode.parentNode.insertBefore(templateRow, target.parentNode.parentNode.nextSibling);

            target.removeEventListener('click', Templates.getTemplateData);
            PR.prettyPrint(); // Use google code prettify to pre format the template (https://github.com/google/code-prettify)
            target.addEventListener('click', Templates.toggleRow);
        }
    };
};

Templates.toggleRow = function () {
    const templateId = this.dataset.typo3TemplateId;
    let row = document.getElementById(templateId);

    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
};

window.addEventListener('load', Templates.initialize, false);
