/*** GESTION DES ERREURS JAVASCRIPT ***/

/* Par defaut, ce script envoi les messages d'erreur javascript uniquement
   Pour intégrer d'autres données qui pourrait être utiles, redéfinir le callback onerror */

var xhrEnvoiErreur = new XMLHttpRequest();
var erreurOccurence = 0;
var errorsWaiting = [];


onerror = function(e) {
    envoiErreur(e);     // Possibilité d'envoyer des objets pour plus d'informations
};


// Envoi les données en attentes
xhrEnvoiErreur.onreadystatechange = function() {
    if(errorsWaiting.length === 0 || (xhrEnvoiErreur.readyState !== 0 && xhrEnvoiErreur.readyState !== 4))
        return false;

    var data = errorsWaiting.shift();
    envoiErreur(data);
};


function envoiErreur(data) {
    if(xhrEnvoiErreur.readyState !== 0 && xhrEnvoiErreur.readyState !== 4) {    // XHR est en cours d'utilisation
        errorsWaiting.push(deepCopy(data));
        return "NOT READY";
    }

    erreurOccurence++;

    xhrEnvoiErreur.open("POST", "ajax/envoiErreur.php", true);
    xhrEnvoiErreur.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    if(typeof(data) === "object")
        xhrEnvoiErreur.send("type=json&occurence=" + erreurOccurence + "&data=" + JSON.stringify(data));
    else
        xhrEnvoiErreur.send("type=string&occurence=" + erreurOccurence + "&data=" + data);
    
    return "SEND";
}


