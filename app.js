/* ========================================================================
 * Custom import of Bootstrap modules
 * comment/uncomment the things you do/don't need.
 * import then needed Font Awesome functionality
 * ======================================================================== */
import 'bootstrap/js/dist/button';
import 'bootstrap/js/dist/collapse';
import 'bootstrap/js/dist/tab';
import 'bootstrap/js/dist/offcanvas';

import frontend from 'parentThemeScripts/components/functions-frontend';
import modal from './components/modal';
import blockGallery from './components/block-gallery';
import gifty from './components/gifty';
import menukaartToggle from '../../../blocks/menukaart';
import openingHoursPro from '../../../blocks/opening-hours-pro';
import googlereviewSlider from '../../../blocks/google-review-slider';

import { domReady } from 'parentThemeScripts/components/domReady';

modal();
gifty();
/**
 * Run the application when the DOM is ready.
 */
domReady(() => {
    frontend();
    blockGallery();
    openingHoursPro(document);
    menukaartToggle(document);
    googlereviewSlider();
});

/**
 * Run the application when the jQuery is ready.
 * We encourage you to use vanilla JS, but if you prefer, you can use jQuery by uncommenting the next block.
 */

/* $(() => {
    // import your jQuery code here
}); */

/**
 * Accept module updates
 *
 * @see https://webpack.js.org/api/hot-module-replacement
 */
import.meta.webpackHot?.accept(console.error);
