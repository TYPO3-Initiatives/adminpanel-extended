/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
const Signals = {};

Signals.initializeSignalDetails = function () {
    const signallinks = document.querySelectorAll('[data-typo3-role=signal-link]');
    for (let i = 0; i < signallinks.length; i++) {
        signallinks[i].addEventListener("click", Signals.getSignalData);
    }
};


Signals.getSignalData = function (event) {
    event.preventDefault();
    const uri = this.dataset.typo3AjaxUrl;
    const request = new XMLHttpRequest();
    request.open('GET', uri);
    request.send();

    function buildInnerRow(datum, k, innerTable) {
        let innerRow = document.createElement('tr');
        const key = document.createElement('td');
        key.innerText = k;
        const val = document.createElement('td');
        val.innerText = datum[k];
        innerRow.appendChild(key);
        innerRow.appendChild(val);
        innerTable.appendChild(innerRow);
    }

    function buildInnerTable(datum, col) {
        let divElement = document.createElement('div');
        divElement.classList.add('typo3-adminPanel-table-overflow');
        let innerTable = document.createElement('table');
        innerTable.classList.add('typo3-adminPanel-table');
        for (let k in datum) {
            if (datum.hasOwnProperty(k)) {
                buildInnerRow(datum, k, innerTable);
            }
        }
        divElement.appendChild(innerTable);
        col = divElement;
        return col;
    }

    function buildSignalDataTable(myArr) {
        const table = document.createElement('table');
        table.classList.add('typo3-adminPanel-table');
        for (let item in myArr.data) {
            if (myArr.data.hasOwnProperty(item)) {
                const row = document.createElement('tr');
                let col = document.createElement('td');
                let datum = myArr.data[item];
                if (typeof(datum) === "object") {
                    col = buildInnerTable(datum, col);
                } else {
                    col.innerText = datum;
                }
                row.appendChild(col);
                table.appendChild(row);
            }
        }
        return table;
    }

    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const myArr = JSON.parse(this.responseText);
            const target = document.querySelector('[data-typo3-signal-id=' + myArr.signalId + ']');
            target.removeEventListener('click', Signals.getSignalData);
            const div = document.createElement('div');
            div.innerHTML = '';
            div.classList.add('typo3-adminPanel-table-overflow');
            target.parentNode.replaceChild(div, target);
            const table = buildSignalDataTable(myArr);
            div.appendChild(table);
        }
    };
};

window.addEventListener('load', Signals.initializeSignalDetails, false);
