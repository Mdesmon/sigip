form_textannu.onsubmit = function() {
    if(datenaissance.value === "") {
        datenaissance.click();
        return false;
    }
};

finfonction.onchange = function() {
    dateff.disabled = !this.checked;
};


/* Pickadate */

$.extend($.fn.pickadate.defaults, {
    selectYears: true,
    // Translations
    monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
    weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
    today: 'Aujourd\'hui',
    clear: 'Effacer',
    close: 'Fermer',
    labelMonthNext: 'Suivant',
    labelMonthPrev: 'Précédent',
    // Format
    format: 'dd/mm/yyyy',
    formatSubmit: 'yyyy/mm/dd'
});

$('#datenaissance').pickadate({
    max: 'picker__day--today'
});
$('#dateff').pickadate();