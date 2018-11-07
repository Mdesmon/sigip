
/* STRUCTURES */
function lecteur_audio(style, element_dest, options) {
    if(style == undefined)
        style = "defaut";
    if(element_dest == undefined)
        element_dest = document.getElementsByTagName('body')[0];
    

    /* INITIALISATION */
    this.audio = document.createElement('audio');
    this.interface = document.createElement('div');
    this.menu = document.createElement('div');
    this.progressBar = document.createElement('div');
    this.progressBar_actual = document.createElement('div');
    this.progressBar_buffer = document.createElement('div');
    this.progressBar_cursor = document.createElement('div');
    this.btn_lecture = document.createElement('div');
    this.btn_stop = document.createElement('div');
    this.timer_actual = divTxt("auto", "00:00");
    this.timer_end = divTxt("auto", "00:00");

    this.audio.lecteur = this;
    this.interface.lecteur = this;
    this.interface.className = "lecteur_audio " + style;
    this.menu.className = "menu";
    this.btn_lecture.className = "lecture";    
    this.btn_stop.className = "stop";
    this.mediaList = [];
    // PROGRESS BAR
    this.progressBar.className = "progressBar";
    this.progressBar_actual.className = "progressBar_actual";
    this.progressBar_buffer.className = "progressBar_buffer";
    this.progressBar_cursor.className = "progressBar_cursor";
    // TIMER
    this.timer_actual.className = "timer";
    this.timer_end.className = "timer";
    var timer_slash = divTxt("34", "/");
    timer_slash.className = "timer";
    
    this.menu.appendChild(widthSpace(20));
    this.menu.appendChild(this.btn_lecture);
    this.menu.appendChild(this.btn_stop);
    this.menu.appendChild(widthSpace(15));
    this.menu.appendChild(this.timer_actual);
    this.menu.appendChild(timer_slash);
    this.menu.appendChild(this.timer_end);
    this.progressBar.appendChild(this.progressBar_buffer);
    this.progressBar.appendChild(this.progressBar_actual);
    this.progressBar.appendChild(this.progressBar_cursor);
    this.interface.appendChild(this.progressBar);
    this.interface.appendChild(this.menu);

    element_dest.appendChild(this.interface);

    /* SET POSITION AND SIZE */
    if(options !== undefined) {
        this.interface.style.left = options.x + "px";
        this.interface.style.top = options.y + "px";
        this.interface.style.bottom = "auto";
        this.interface.style.width = options.w + "px";
        this.menu.style.height = options.h + "px";
        this.menu.style.lineHeight = options.h + "px";
    }


    /* FONCTIONS PUBLIQUES */
    this.stop = function() {
        this.audio.pause();
        this.audio.currentTime = 0;
        this.btn_lecture.className = "lecture";
    }

    this.loadMedia = function(media) {                                  // Charge le media (url, input, File)
        if(typeof(media) === "string")                                  // Est une URL
            this.audio.src = media;
        if(window.URL === undefined)                                    // l'objet URL n'est pas géré. IE < 10
            this.trigger_error("Le média ne peux être chargé car votre navigateur n'est pas compatible HTML5. Veuillez mettre a jour votre navigateur pour corriger ce problème.");
        else if(media.type === "file")                                  // Est un input
            this.audio.src = URL.createObjectURL(media.files[0]);
        else if(typeof(media) === "object")                             // Est un File
            if(media.constructor.name === "File")
                this.audio.src = URL.createObjectURL(media);
        else
            this.trigger_error("Le média n'a pas été reconnu.");
    }

    this.addToList = function(media) {
        if(Array.isArray(media))
            mediaList.concat(media);
        else
            mediaList.push(media);
    }

    this.detruire = function() {
        this.audio.src = "";
        remove(this.interface);
    }



    /* FONCTIONS PRIVEES */
    this.updateCurrentTime = function() {
        var t = getFormatedTime(this.audio.currentTime);

        if(t.h === 0)
            this.timer_actual.innerHTML = t.m + ":" + zerofill(t.s, 2);
        else
            this.timer_actual.innerHTML = t.h + ":" + zerofill(t.m, 2) + ":" + zerofill(t.s, 2);

        /* PROGRESS BAR */
        var w = this.progressBar.offsetWidth;
        var ratio = this.audio.currentTime / this.audio.duration;
        var positionX = w*ratio;

        this.progressBar_actual.style.width = positionX + "px";
        this.progressBar_cursor.style.left = positionX + "px";
    }



    this.trigger_error = function(msg) {
        var evt = new ErrorEvent('error', {message: msg});
        this.audio.dispatchEvent(evt);
    };


    /* EVENTS */
    this.interface.onmousedown = function(e) {
        e.preventDefault();
    };

    this.btn_lecture.onclick = function() {
        var lecteur = this.parentElement.parentElement.lecteur;

        if(hasClass("pause", this)) {
            removeClass("pause", this);
            addClass("lecture", this);
            lecteur.audio.pause();
        }
        else {
            this.className = "";
            addClass("pause", this);
            lecteur.audio.play();
        }
    };

    this.btn_stop.onclick = function() {
        var lecteur = this.parentElement.parentElement.lecteur;
        lecteur.stop();
    };

    this.progressBar.onmousedown = function(e) {
        var audio = this.parentElement.lecteur.audio;
        var w = this.offsetWidth;
        var pos = e.clientX - getScreenPositionLeft(this);
        var ratio = pos/w;
        audio.currentTime = ratio*audio.duration;
    };

    addEvent(this.audio, "canplay", function() {
        var t = getFormatedTime(this.duration);
        
        this.lecteur.progressBar.style.display = "block";

        if(t.h === 0)
            this.lecteur.timer_end.innerHTML = t.m + ":" + zerofill(t.s, 2);
        else
            this.lecteur.timer_end.innerHTML = t.h + ":" + zerofill(t.m, 2) + ":" + zerofill(t.s, 2);
        
        this.lecteur.updateCurrentTime();
    });

    addEvent(this.audio, "timeupdate", function() {
        this.lecteur.updateCurrentTime();
    });

    addEvent(this.audio, "progress", function() {
        var buf = this.buffered;

        if(buf.length === 1) {
            var buffuredTime = buf.end(0);
            var ratio = buffuredTime / this.lecteur.audio.duration;
            this.lecteur.progressBar_buffer.style.width = ratio * this.lecteur.progressBar.offsetWidth + "px";
        }
        else
            this.lecteur.progressBar_buffer.style.width = "0";
    });

    addEvent(this.audio, "ended", function() {
        this.lecteur.btn_lecture.className = "recommencer";
    });

}



