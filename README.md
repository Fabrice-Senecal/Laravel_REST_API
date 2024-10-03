# Projet API Laravel : Simulateur de Joueur Battleship

auteurs: Fabrice Senécal et Cameron Chouinard

Ce projet consiste à développer une **API REST en Laravel** qui simule un joueur pour le jeu Battleship. L'API permet de participer à des tournois en fournissant des points d'entrée spécifiques pour interagir avec le jeu.

## Fonctionnalités principales :

- **Démarrer une partie** (`/battleship-ia/parties` - POST) : L'IA place ses bateaux sur une grille 10x10 en respectant les règles du jeu.
- **Jouer un coup** (`/battleship-ia/parties/{id}/missiles` - POST) : L'IA retourne une coordonnée (ex : 'D-4') pour lancer un missile.
- **Recevoir le résultat d'un coup** (`/battleship-ia/parties/{id}/missiles/{coordonnées}` - PUT) : L'IA est informée si le tir a touché ou non un bateau ennemi.
- **Terminer une partie** (`/battleship-ia/parties/{id}` - DELETE) : Indique à l'IA que la partie est terminée.

## Caractéristiques techniques :

- **Authentification Bearer Token** : Sécurise les appels à l'API.
- **Algorithme avancé** : L'IA utilise des stratégies intelligentes pour optimiser ses tirs, comme cibler les cases adjacentes après une touche.
- **Structure orientée objet** : Le code est organisé pour être modulable et maintenable.
- **Gestion des multiples parties** : Permet à un joueur de participer à plusieurs parties simultanément.

Cette API ne gère pas le déroulement complet du jeu, mais simule un joueur qui interagit avec un client externe chargé de la logique du jeu.

