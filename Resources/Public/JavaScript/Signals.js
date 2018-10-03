function getSignalData(event) {
	event.preventDefault();
	const uri = this.dataset.typo3AjaxUrl;
	const request = new XMLHttpRequest();
	request.open('GET', uri);
	request.send();
	request.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			const myArr = JSON.parse(this.responseText);
			const target = document.querySelector('[data-typo3-signal-id=' + myArr.signalId + ']');
			target.removeEventListener('click', getSignalData);
			const div = document.createElement('div');
			div.innerHTML = '';
			div.classList.add('typo3-adminPanel-table-overflow');
			target.parentNode.replaceChild(div, target);
			const table = document.createElement('table');
			table.classList.add('typo3-adminPanel-table');
			for (let item in myArr.data) {
				const row = document.createElement('tr');
				let col = document.createElement('td');
				if (myArr.data.hasOwnProperty(item)) {
					let datum = myArr.data[item];
					if (typeof(datum) === "object") {
						let divElement = document.createElement('div');
						divElement.classList.add('typo3-adminPanel-table-overflow');
						let innerTable = document.createElement('table');
						innerTable.classList.add('typo3-adminPanel-table');
						for (k in datum) {
							let innerRow = document.createElement('tr');
							if (datum.hasOwnProperty(k)) {
								const key = document.createElement('td');
								key.innerText = k;
								const val = document.createElement('td');
								val.innerText = datum[k];
								innerRow.appendChild(key);
								innerRow.appendChild(val);
							}
							innerTable.appendChild(innerRow);
						}
						divElement.appendChild(innerTable);
						col = divElement;
					} else {
						col.innerText = datum;
					}
					row.appendChild(col);
					table.appendChild(row);
				}
			}
			div.appendChild(table);
		}
	};
}

function initializeSignalDetails() {
	const signallinks = document.querySelectorAll('[data-typo3-role=signal-link]');
	for (let i = 0; i < signallinks.length; i++) {
		signallinks[i].addEventListener("click", getSignalData)
	}
}

window.addEventListener('load', initializeSignalDetails, false);