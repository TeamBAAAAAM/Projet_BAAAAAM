
--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`CodeC`, `NomC`, `DesignationC`, `StatutC`) VALUES
(1, 'interim', 'Je suis interimaire et/ou j\'ai un emploi saisonnier', 'Actif'),
(2, 'cesu', 'Je suis indemnisé·e par CESU / PAJEMPLOI ou je suis assistant·e maternel·le', 'Actif'),
(3, 'pole-emploi', 'Je suis indemnisé·e par Pôle Emploi', 'Actif'),
(4, 'pole-emploiC', 'J\'exerce une activité salariée avec un complément Pôle Emploi', 'Actif'),
(5, 'independant', 'Je suis travailleur indépendant et j\'attends un enfant', 'Actif'),
(6, 'art-aut', 'Je suis artiste auteur', 'Actif'),
(7, 'intermit', 'Je suis intermittent·e du spectacle', 'Actif'),
(8, 'salarie', 'Je suis salarié·e', 'Actif');


--
-- Déchargement des données de la table `listemnemonique`
--

INSERT INTO `listemnemonique` (`CodeM`, `Mnemonique`, `Designation`) VALUES
(1, 'BS', 'Bulletin de salaire'),
(2, 'JUSTIF_SAL', 'Autres justificatifs de salaire'),
(3, 'ATT_SAL', 'Attestation de salaire'),
(4, 'PJ_IJ', 'Pièces justificatives IJ');


--
-- Déchargement des données de la table `concerner`
--

INSERT INTO `concerner` (`CodeC`, `CodeM`, `Label`) VALUES
(1, 1, 'Les bulletins de salaire des 12 mois précédant l\'arrêt de travail (de tous vos employeurs)'),
(2, 1, 'Les bulletins de salaire des 12 mois précédant l\'arrêt de travail (de tous vos employeurs)'),
(3, 1, 'Les bulletins de salaire des 4 mois précédant l\'arrêt de travail (de tous vos employeurs)'),
(4, 1, 'Les bulletins de salaire des 3 mois précédant l\'arrêt de travail (de tous vos employeurs)'),
(7, 2, 'Cachet du GUSO'),
(8, 3, 'Attestation de salaire délivrée par votre employeur'),
(6, 4, 'Imprimé délivré par AGESSA');
