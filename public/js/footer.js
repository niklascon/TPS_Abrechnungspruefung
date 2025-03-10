// Sicherstellen, dass der Footer nach allen anderen Elementen der Seite eingefügt wird
window.addEventListener('load', function() {
    const footer = document.querySelector('.footer');
    const dynamicContent = document.getElementById('dynamic-content');

    console.log('Footer:', footer);
    console.log('Dynamic Content:', dynamicContent);

    // Überprüfen, ob Inhalt geladen wurde und Footer korrekt positioniert ist
    if (dynamicContent.nextElementSibling !== footer) {
        document.body.appendChild(footer); // Footer ans Ende der Seite anhängen
    }
});
