import {Controller} from '@hotwired/stimulus';
import Sortable from 'sortablejs';

export default class _class extends Controller {

    static targets = []

    static values = {
        updateUrl: String,
        animation: String,
        handle: String
    }

    initialize() {
        this.end = this.end.bind(this);
        this.update = this.update.bind(this);
    }

    connect() {
        this.sortable = new Sortable(this.element, {
            ...this.defaultOptions,
            ...this.options
        });
    }

    disconnect() {
        this.sortable.destroy();
        this.sortable = undefined;
    }

    async updateSync(questions) {

        const response = await fetch(`${this.updateUrlValue}`, {
            method: 'POST',
            body: JSON.stringify({'questions': questions}),
            headers: {
                'Content-type': 'application/json; charset=UTF-8'
            }
        });

        let responseString = await response.text();

        var data = JSON.parse(responseString);
        if (data.error) {

        } else {

        }
    }

    // Changed sorting within list
    update(evt) {

        let list = evt.from;
        var radios = list.querySelectorAll('.list-group-item');
        let clients = [];

        Array.prototype.forEach.call(radios, function (el, i) {
            clients.push(el.dataset.id);
        });

        this.updateSync(clients)
    }

    end({
            item,
            newIndex
        }) {

    }

    get options() {
        return {
            animation: this.animationValue || this.defaultOptions.animation || 150,
            handle: this.handleValue || this.defaultOptions.handle || undefined,
            onEnd: this.end,
            onUpdate: this.update
        };
    }

    get defaultOptions() {
        return {};
    }

}

