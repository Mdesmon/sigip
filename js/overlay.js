
var timeout_bandeau = null;


// CONSTANTES
var MESSAGE_DURATION = 6000;


// INITIALISATION

var overlay_bandeau = document.createElement('div');
var overlay_bandeau_icon = document.createElement('div');
var overlay_bandeau_message = document.createElement('div');
overlay_bandeau.id = "overlay_bandeau";
overlay_bandeau_icon.id = "overlay_bandeau_icon";
overlay_bandeau_message.id = "overlay_bandeau_message";
overlay_bandeau.appendChild(overlay_bandeau_icon);
overlay_bandeau.appendChild(overlay_bandeau_message);

var overlay_voile = document.createElement('div');
overlay_voile.id = "overlay_voile";


document.body.appendChild(overlay_voile);
document.body.appendChild(overlay_bandeau);



// EVENTS

addEvent(overlay_bandeau, "click", function(){
    clearTimeout(timeout_bandeau);
    fadeOut(overlay_bandeau, "fondu_reverse", 500);
});



// FONCTIONS

function afficherVoile(visible, color) {
    if(visible)
        overlay_voile.style.display = "block";
    else
        overlay_voile.style.display = "none";

    if(color !== undefined)
        overlay_voile.style.backgroundColor = color;
}

function afficherMessage(overlay, msg, icon) {
    var overlayElement = null;
    var messageElement = null;
    var iconElement = null;


    if(overlay === "BANDEAU") {
        overlay_bandeau.style.display = "block";
        overlay_bandeau.style.width = getInnerWidth()-20 + "px"; // -20 pour le padding

        /* Animation */
        clearTimeout(timeout_bandeau);          // Annulation d'un éventuel delay de lancement de fadeOut()
        cancelFadeOut(overlay_bandeau);      // Arrete un éventuel fadeOut() en cours d'execution

        timeout_bandeau = setTimeout(function(){
            fadeOut(overlay_bandeau, "fondu_reverse", 1000);
        }, MESSAGE_DURATION);

        overlayElement = overlay_bandeau;
        messageElement = overlay_bandeau_message;
        iconElement = overlay_bandeau_icon;
    }

    overlayElement.style.display = "block";

    


    /* Message */
    messageElement.innerHTML = msg;

    /* icon */
    if(icon !== undefined)
        iconElement.className = "icon " + icon;
    else
        iconElement.className = "";
}