import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ['result', 'list']

    static values = {
        url: String,
    }

    selectX(event) {
        let list = this.listTarget;
        let index = list.childElementCount;
        this.search(index)
    }

    async search(index) {
        const params = new URLSearchParams({
            index: index,
        });
        const response = await fetch(`${this.urlValue}?${params.toString()}`);
        let txt = await response.text();
        const liElement = this.createElementFromHTML(txt);
        this.listTarget.appendChild(liElement);
    }

    createElementFromHTML(htmlString) {
        const div = document.createElement('div');
        div.innerHTML = htmlString.trim();
        return div.firstElementChild;
    }
}
