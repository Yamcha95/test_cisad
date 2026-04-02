# 🏆 Plateforme de Gestion de Joueurs (Test Technique)

Ce projet est une application de gestion de base de données de joueurs réalisée avec Symfony 7. Elle permet d'administrer des profils utilisateurs et leurs statistiques de ligue.

## 🚀 Installation rapide

1. **Clonage & Dépendances** :
   ```bash
   git clone https://github.com/Yamcha95/test_cisad.git
   cd <nom-du-dossier>
   composer install
Configuration :
Adaptez votre DATABASE_URL dans le fichier .env ou .env.local.

2. **Base de données** :
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    
3. **Serveur**:
    ```bash
    symfony serve


🛠️ Choix Techniques & Justifications
🔒 Sécurité et Validation
Pour ce test, j'ai mis en place des mesures de sécurité équilibrées entre protection et expérience utilisateur :

Exigences du Mot de Passe : J'ai configuré une contrainte de 8 caractères minimum incluant au moins un chiffre et une lettre.

Justification : C'est le standard actuel pour empêcher les attaques par force brute simples, tout en restant accessible pour un environnement de test ou de jeu.

Choix du Rôle à l'inscription : L'utilisateur peut choisir son rôle (ROLE_USER ou ROLE_ADMIN) via le formulaire.

Justification : Ce choix a été fait pour faciliter vos tests. Cela vous permet de basculer instantanément entre une vue "Standard" et une vue "Admin" sans avoir à modifier manuellement la base de données.

Gestion des erreurs d'import (CSV) : L'importation est sécurisée par un bloc try...catch.

Justification : En cas de doublon d'email ou de format erroné, l'application ne crash pas ; elle informe l'utilisateur via un message d'alerte (Flash Message), garantissant la robustesse de l'outil.

🎨 Design et Ergonomie (Frontend)
J'ai fait le choix d'utiliser Bootstrap 5 pour l'ensemble de l'interface :

Intuitivité : Utilisation de composants familiers (Cartes, Tableaux, Badges) pour une prise en main immédiate.

Code couleur des Rangs : Les rangs (Iron, Bronze, Gold...) sont associés à des badges colorés.

Justification : Cela permet une lecture visuelle instantanée des performances globales des joueurs sans analyser les chiffres.

Accessibilité & Simplicité : Le framework garantit une interface claire et "responsive", essentielle pour un outil d'administration efficace.
