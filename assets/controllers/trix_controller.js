import { Controller } from '@hotwired/stimulus';

/*
 * Loads the Trix rich text editor only on pages that contain a TrixEditorType field.
 * The <trix-editor> element keeps the hidden input it is linked to in sync.
 */
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    async connect() {
        await Promise.all([import('trix'), import('trix/dist/trix.min.css')]);

        // File uploads are not handled server side: refuse attachments dropped or pasted into the editor
        this.element.addEventListener('trix-file-accept', (event) => event.preventDefault());
    }
}
