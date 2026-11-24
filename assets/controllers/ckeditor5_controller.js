import { Controller } from '@hotwired/stimulus';

/*
 * Loads CKEditor 5 only on pages that contain a CKEditor5Type field.
 * The <cke5-editor> custom element is upgraded as soon as it is defined,
 * and keeps the hidden input of the form in sync.
 */
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    async connect() {
        await Promise.all([import('cke5-styles'), import('@mati365/ckeditor5-symfony')]);
    }
}
