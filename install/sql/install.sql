CREATE TABLE IF NOT EXISTS Inscriptions (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	utilisateur int(11) unsigned NOT NULL,
	session int(11) unsigned NOT NULL,
	PRIMARY KEY (id),
	KEY inscriptions_unique (utilisateur,session),
	KEY session (session)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Listes_emails (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	numero int(11) unsigned NOT NULL,
	email varchar(40) NOT NULL,
	PRIMARY KEY (id),
	KEY numero (numero)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Logs (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	utilisateur int(11) unsigned NULL,
	organisation int(11) unsigned DEFAULT NULL,
	type int(11) unsigned NOT NULL,
	datetime_creation datetime NOT NULL,
	commentaire text,
	PRIMARY KEY (id),
	KEY organisation (organisation),
	KEY type (type),
	KEY utilisateur (utilisateur)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Organisations (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	nom varchar(80) NOT NULL,
	parent int(11) unsigned DEFAULT NULL COMMENT 'Permet de creer une sous organisation',
	PRIMARY KEY (id),
	UNIQUE KEY organisations_unique (nom,parent),
	KEY parent (parent)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Parametres_generaux (
	liste_emails_inscription int(11) unsigned NOT NULL COMMENT 'Numero de liste de mail a utiliser pour chaque inscription',
	timeout_connection time DEFAULT NULL,
	KEY liste_emails_inscription (liste_emails_inscription)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO Parametres_generaux (liste_emails_inscription, timeout_connection) VALUES
(0, '00:05:00');

CREATE TABLE IF NOT EXISTS Presence (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	date_Presence date NOT NULL,
	heure_Presence time NOT NULL,
	groupe_travail int(11) unsigned NOT NULL,
	liste_apprenants text NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Responses (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	user int(11) unsigned NOT NULL,
	session int(11) unsigned NOT NULL,
	page int(11) unsigned NULL,
	data mediumtext NOT NULL,
	send datetime DEFAULT NULL,
	PRIMARY KEY (id),
	KEY user (user),
	KEY session (session)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Sessions (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	nom varchar(250) NOT NULL,
	organisation int(11) unsigned NOT NULL,
	auteur int(11) unsigned NOT NULL,
	date_debut date NOT NULL,
	date_fin date NOT NULL,
	statut int(11) unsigned NOT NULL,
	code char(5) NOT NULL,
	formateur_connecte varchar(40) DEFAULT NULL,
	derniere_action datetime DEFAULT NULL,
	version varchar(50) NOT NULL COMMENT 'Sous quel version de l''application le fichier à été généré',
	PRIMARY KEY (id),
	UNIQUE KEY unique_nom_organisation (nom,organisation),
	UNIQUE KEY code (code),
	KEY formateur_connecte (formateur_connecte),
	KEY organisation (organisation),
	KEY auteur (auteur)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Swap (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	utilisateur int(11) unsigned NOT NULL,
	session int(11) unsigned NOT NULL,
	action mediumtext NOT NULL,
	PRIMARY KEY (id),
	KEY utilisateur (utilisateur),
	KEY session (session)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Types_log (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	type varchar(30) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY type (type)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO Types_log (id, type) VALUES
(1, 'INCIDENT'),
(2, 'DIVERS'),
(3, 'MODIFICATION UTILISATEUR'),
(4, 'MODIFICATION SESSION');

CREATE TABLE IF NOT EXISTS Types_utilisateurs (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	type varchar(30) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY type (type)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO Types_utilisateurs (id, type) VALUES
(1, 'apprenant'),
(2, 'formateur'),
(3, 'superviseur'),
(4, 'administrateur'),
(5, 'super-admin');

CREATE TABLE IF NOT EXISTS Utilisateurs (
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	nom varchar(40) NOT NULL,
	prenom varchar(40) NOT NULL,
	organisation int(11) unsigned DEFAULT NULL,
	email varchar(40) NOT NULL,
	type_utilisateur int(11) unsigned NOT NULL,
	nom_utilisateur varchar(40) NOT NULL,
	mdp varchar(255) NOT NULL,
	etat varchar(255) NOT NULL DEFAULT 'DECONNECTE',
	datetime_etat datetime DEFAULT NULL,
	session_actuelle int(11) unsigned DEFAULT NULL,
	derniere_action datetime DEFAULT NULL,
	dossier varchar(255) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY nom_utilisateur (nom_utilisateur),
	KEY organisation (organisation)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE Inscriptions
	ADD CONSTRAINT fk_inscriptions_session FOREIGN KEY (session) REFERENCES Sessions (id),
	ADD CONSTRAINT fk_inscriptions_utilisateur FOREIGN KEY (utilisateur) REFERENCES Utilisateurs (id);

ALTER TABLE Logs
	ADD CONSTRAINT fk_logs_organisation FOREIGN KEY (organisation) REFERENCES Organisations (id),
	ADD CONSTRAINT fk_logs_type FOREIGN KEY (type) REFERENCES Types_log (id),
	ADD CONSTRAINT fk_logs_utilisateur FOREIGN KEY (utilisateur) REFERENCES Utilisateurs (id);

ALTER TABLE Organisations
	ADD CONSTRAINT fk_organisations_parent FOREIGN KEY (parent) REFERENCES Organisations (id);

ALTER TABLE Sessions
	ADD CONSTRAINT fk_sessions_organisation FOREIGN KEY (organisation) REFERENCES Organisations (id),
	ADD CONSTRAINT fk_sessions_auteur FOREIGN KEY (auteur) REFERENCES Utilisateurs (id);

ALTER TABLE Swap
	ADD CONSTRAINT fk_swap_session FOREIGN KEY (session) REFERENCES Sessions (id),
	ADD CONSTRAINT fk_swap_utilisateur FOREIGN KEY (utilisateur) REFERENCES Utilisateurs (id);

ALTER TABLE Responses
	ADD CONSTRAINT fk_responses_user FOREIGN KEY (user) REFERENCES Utilisateurs (id),
	ADD CONSTRAINT fk_responses_session FOREIGN KEY (session) REFERENCES Sessions (id);

ALTER TABLE Utilisateurs
	ADD CONSTRAINT fk_utilisateurs_organisation FOREIGN KEY (organisation) REFERENCES Organisations (id)