import 'bootstrap/js/dist/collapse';
import 'bootstrap/js/dist/tab';

import menukaartToggle from '../../../blocks/menukaart';
import googlereviewSlider from '../../../blocks/google-review-slider';
import domReady from '@wordpress/dom-ready';
import backend from 'parentThemeScripts/components/functions-backend';

backend();

// MENUKAART INIT
function initMenukaart(el) {
    if (!el || el.dataset.menukaartInitialized === 'true') return;
    menukaartToggle(document);
    el.dataset.menukaartInitialized = 'true';
}

// GOOGLE REVIEW SLIDER INIT
function initGoogleReviewSlider(el) {
    if (!el) return;
    el.dataset.googleReviewSliderInitialized = 'true'; // Markeren, maar niet blokkeren
    googlereviewSlider(el);
}

// OBSERVE PREVIEW BUTTON
function observePreviewButtonClick() {
    const observer = new MutationObserver(() => {
        const previewButton = document.querySelector('button[aria-label="Schakel naar voorbeeld"]');
        if (!previewButton || previewButton.dataset.observed === 'true') return;

        previewButton.dataset.observed = 'true';

        previewButton.addEventListener('click', () => {
            setTimeout(() => {
                const el = document.querySelector('.acf-block-preview.is-selected');
                if (!el) return;

                if (el.classList.contains('menukaart')) {
                    el.dataset.menukaartInitialized = 'false';
                    menukaartToggle(document);
                    el.dataset.menukaartInitialized = 'true';
                }

                if (el.classList.contains('google-review-slider')) {
                    el.dataset.googleReviewSliderInitialized = 'false';
                    googlereviewSlider(el);
                    el.dataset.googleReviewSliderInitialized = 'true';
                }
            }, 100);
        });
    });

    // Observeer telkens wanneer een blok geselecteerd wordt
    const selectedBlockObserver = new MutationObserver(() => {
        observePreviewButtonClick();
    });

    const blockList = document.querySelector('.block-editor-block-list__layout');
    if (blockList) {
        selectedBlockObserver.observe(blockList, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class'],
        });
    }

    observer.observe(document.body, { childList: true, subtree: true });
}

domReady(() => {
    acf.addAction('render_block_preview', (el) => {
        const wrapper = el?.[0];
        if (!wrapper) return;

        if (wrapper.classList.contains('menukaart')) {
            initMenukaart(wrapper);
        }

        if (wrapper.classList.contains('google-review-slider')) {
            initGoogleReviewSlider(wrapper);
        }
    });

    acf.addAction('ready', () => {
        document.querySelectorAll('.acf-block-preview.menukaart').forEach(initMenukaart);
        document.querySelectorAll('.acf-block-preview.google-review-slider').forEach(initGoogleReviewSlider);
        observePreviewButtonClick();
    });

    observePreviewButtonClick();
});
