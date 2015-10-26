#Role
Le Dossier *.content* est initialement prévu pour accueillir les mises à jour de contenu (par exemple, l'ajout automatique de pages en BDD).

#Quand
Toutes les mises à jours présentes dans ce dossier auront lieu après celles des dossier update et upgrade (quelque soit la date).

#Pourquoi
Ceci afin d'éviter des erreurs de php lors de l'instanciation d'un objet ne correspondant pas encore à la BDD.

#Comment
Par convention, nous utiliserons la date ISO du jour suivie d'un identifiant numérique (0000-00-00-0).

- Créer un dossier suivant cette nomenclature

- Y ajouter des fichier php ou sql (voir sh) qui seront executés par ordre alphabétiques

- Modifier le fichier last_version.txt de sorte à ce que sont contenu soit le nom du dernier dossier de mise à jour (ex : 0000-00-00-0)

- Les mises à jours sont prêtes à être traitées
