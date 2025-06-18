export default function openingHoursPro(scope = document) {
    const blocks = scope.querySelectorAll('.opening-hours-pro.acf-block');

    blocks.forEach((block) => {
        const blockId = block.dataset.blockInstanceId || '';
        // Laat loader zien (staat standaard al in Twig)
        // Doe AJAX request naar je REST endpoint
        fetch(`/wp-json/wp-lemon/v1/opening-hours-pro?block_id=${blockId}`)
            .then((response) => response.text())
            .then((html) => {
                block.innerHTML = html;
            })
            .catch(() => {
                block.innerHTML = '<div class="opening-hours-error">Kan openingstijden niet laden.</div>';
            });
    });
}
