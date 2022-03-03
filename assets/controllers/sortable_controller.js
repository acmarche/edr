function _defineProperty(obj, key, value) {
    if (key in obj) {
        Object.defineProperty(obj, key, {value: value, enumerable: true, configurable: true, writable: true});
    } else {
        obj[key] = value;
    }
    return obj;
}

import {Controller} from 'stimulus';
import Sortable from 'sortablejs';
import Rails from '@rails/ujs';

//copy past from https://github.com/stimulus-components/stimulus-sortable
//to change method POST
export default class _class extends Controller {
    initialize() {
        this.end = this.end.bind(this);
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

    end({
            item,
            newIndex
        }) {
        if (!item.dataset.sortableUpdateUrl || !window._rails_loaded) return;
        const resourceName = this.resourceNameValue;
        const paramName = this.paramNameValue || 'position';
        const param = resourceName ? `${resourceName}[${paramName}]` : paramName;
        const data = new FormData();
        data.append(param, newIndex + 1);
        Rails.ajax({
            url: item.dataset.sortableUpdateUrl,
            type: 'POST',
            data
        });
    }

    get options() {
        return {
            animation: this.animationValue || this.defaultOptions.animation || 150,
            handle: this.handleValue || this.defaultOptions.handle || undefined,
            onEnd: this.end
        };
    }

    get defaultOptions() {
        return {};
    }

}

_defineProperty(_class, "values", {
    resourceName: String,
    paramName: String,
    animation: Number,
    handle: String
});
