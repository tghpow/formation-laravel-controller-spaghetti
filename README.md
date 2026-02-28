# Du controller spaghetti à un Laravel propre

Démo Laravel : création de factures (formulaire Vue.js), génération PDF (DomPDF), puis refactoring prévu (Observer, etc.).

Suis moi sur linkedin: 
www.linkedin.com/in/thibault-chazottes

Abonnes toi à ma chaine Youtube: 
https://www.youtube.com/@formateurwebThibaultChazottes

J’y parle de Laravel, de Vue.js et des mes projets freelance.  

Reçois ton code promo pour mon cours complet sur Laravel a 9.9$ seulement: https://tally.so/r/aQ6KXb  

Voir mon cours Laravel sur Udemy: https://www.udemy.com/course/laravel-fr/

## Prérequis

- PHP 8.2+
- Composer
- Node.js & npm
- Base de données (MySQL, SQLite, etc.)

## Installation

```bash
# Dépendances
composer install
npm install

# Environnement
cp .env.example .env
php artisan key:generate

# Base de données (adapter DB_* dans .env si besoin)
php artisan migrate
php artisan db:seed

# Front (build une fois, ou npm run dev en dev)
npm run build
```

## Lancer l’app

```bash
php artisan serve
```

Ouvre http://localhost:8000

## Utilisation (démo)

1. **Simuler la connexion** : cliquer sur « Simuler connexion » (en haut à droite). Cela connecte l’utilisateur de démo (admin).
2. **Créer une facture** : le formulaire est pré-rempli ; cliquer sur « Créer la facture ». Le PDF est généré, enregistré dans `storage/app/private/invoices/`, et s’ouvre dans un nouvel onglet.

L’utilisateur de démo : `test@example.com` / mot de passe `password` (si besoin en tinker).

## Stack

- Laravel 12, Vue 3, Vite, Tailwind CSS
- barryvdh/laravel-dompdf pour les PDF
