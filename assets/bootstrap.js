import { startStimulusApp } from '@symfony/stimulus-bridge';
import { Autocomplete } from 'stimulus-autocomplete';
import Lightbox from "stimulus-lightbox"

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

app.register('autocomplete', Autocomplete);
app.register('lightbox', Lightbox);

import Rails from "@rails/ujs"
Rails.start() //pour requet ajax sortable element
