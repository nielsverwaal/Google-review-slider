export default function menukaartToggle(scope = document) {
    const toggle = scope.querySelector('#allergen-toggle');
    if (!toggle || toggle.dataset.toggleInitialized === 'true') return;

    toggle.dataset.toggleInitialized = 'true';
    const switchText = toggle.querySelector('.switch-text');

    // ✅ Initialiseer visuele status direct
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-checked', String(expanded));
    if (switchText) {
        switchText.textContent = expanded ? 'Verberg allergenen' : 'Toon allergenen';
    }

    // ✅ Event listener voor toekomstige clicks
    toggle.addEventListener('click', () => {
        setTimeout(() => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-checked', String(expanded));
            if (switchText) {
                switchText.textContent = expanded ? 'Verberg allergenen' : 'Toon allergenen';
            }
        }, 10);
    });
}
