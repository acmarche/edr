import {Tooltip, Toast, Popover} from 'bootstrap';
import {Controller} from 'stimulus';

export default class extends Controller {
    connect() {
        var options = {'html': true};
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new Popover(popoverTriggerEl, options)
        })
    }
}
