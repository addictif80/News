# Déploiement sur CyberPanel (OpenLiteSpeed) — VM Proxmox

Ce document décrit comment déployer et mettre à jour le site en utilisant
principalement l'interface web de CyberPanel, pour limiter au maximum les
accès SSH.

## 1. Prérequis (à faire une seule fois, en SSH)

Ces étapes sont ponctuelles, à l'installation initiale du serveur :

- PHP 8.3+ avec les extensions : `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`,
  `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
  (CyberPanel installe déjà la plupart de ces extensions avec un site PHP.)
- Composer installé globalement.
- Node.js 20+ si vous souhaitez builder les assets (CSS/JS) directement sur
  le serveur. Sinon, voir l'option CI en section 4.

## 2. Créer le site dans CyberPanel

1. **Websites > Create Website** : créez le domaine, choisissez PHP 8.3.
2. **Databases > Create Database** : créez une base MySQL + un utilisateur
   dédié (notez les identifiants pour le `.env`).
3. Dans **File Manager**, le vhost pointe par défaut sur
   `public_html/`. Le document root de l'application Laravel doit pointer
   sur `public_html/public` — CyberPanel permet de changer le "Document
   Root" du vhost dans **Websites > List Websites > Manage > vHost Conf**
   (ou via `Web Root` dans les réglages du site).

## 3. Déployer le code via Git Manager (sans SSH récurrent)

1. **Websites > Git Manager** (ou `CyberPanel > Manage SSL` selon version) :
   ajoutez le dépôt Git du projet, branche à déployer.
2. Configurez un **script de déploiement post-pull** (CyberPanel propose un
   champ "Deploy Script" / "Post-receive" selon la version) contenant :

   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan filament:optimize
   ```

3. Chaque mise à jour se fait ensuite en cliquant sur **"Pull / Deploy"**
   dans l'interface — aucun SSH nécessaire après la configuration initiale.

Si votre version de CyberPanel ne propose pas de script de déploiement
automatique, ces commandes peuvent être lancées depuis le **Terminal Web**
intégré à CyberPanel (accessible depuis l'interface, pas un vrai SSH client
externe).

## 4. Build des assets (CSS/JS Vite + GrapesJS)

Deux options :

- **Option A (recommandée) : build en CI.** Ajoutez une étape GitHub Actions
  qui exécute `npm ci && npm run build` et commite (ou publie en artifact)
  le dossier `public/build`. Le déploiement CyberPanel n'a alors besoin que
  de PHP/Composer.
- **Option B : build sur le serveur.** Si Node est installé sur la VM,
  ajoutez `npm ci && npm run build` au script de déploiement de la section 3.

## 5. Fichier `.env`

Le `.env` ne contient que le strict minimum ; **tous les réglages
fonctionnels (SMTP, Stripe, veille) se gèrent depuis le panel admin**
(`/admin` > Réglages), pas dans ce fichier.

```env
APP_NAME="Nom du site"
APP_ENV=production
APP_KEY=            # généré une fois via le Terminal Web : php artisan key:generate
APP_DEBUG=false
APP_URL=https://votre-domaine.tld

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

# Requis uniquement pour amorcer Cashier avant la première sauvegarde
# des réglages Stripe dans le panel :
STRIPE_SUBSCRIPTION_PRICE_ID=price_xxx
```

## 6. Cron Jobs (planificateur Laravel) — 100% interface web

Dans **CyberPanel > Cron Jobs**, ajoutez une seule tâche :

```
* * * * * php /home/votre-domaine/public_html/artisan schedule:run >> /dev/null 2>&1
```

Cette unique tâche cron pilote tout :
- `veille:run` toutes les 15 minutes (interne à `routes/console.php`),
  qui elle-même respecte l'intervalle configuré dans le panel
  (Réglages > Veille informationnelle).
- `queue:work --stop-when-empty` chaque minute, pour traiter les emails de
  newsletter et autres jobs en attente — pas besoin de Supervisor ni de
  process persistant à surveiller en SSH.

## 7. SSL

**Websites > List Websites > Manage > SSL** : émission Let's Encrypt en un
clic, renouvellement automatique géré par CyberPanel.

## 8. Stripe

1. Dans le panel admin (`/admin` > Réglages > Stripe), renseignez la clé
   publique, la clé secrète et le secret de webhook.
2. Dans le dashboard Stripe, configurez le webhook vers :
   `https://votre-domaine.tld/stripe/webhook`
   Événements à écouter au minimum : `customer.subscription.created`,
   `customer.subscription.updated`, `customer.subscription.deleted`,
   `invoice.payment_succeeded`, `invoice.payment_failed`.

## 9. SMTP et Newsletter

Les identifiants SMTP se configurent dans `/admin` > Réglages > Serveur
SMTP. Ils sont lus dynamiquement à chaque envoi (aucun redéploiement requis
pour changer de fournisseur SMTP).

## 10. Résumé : ce qui nécessite le SSH (une seule fois)

- Installation de PHP/Composer/Node si absents.
- `php artisan key:generate` initial.
- Configuration du Document Root vers `public/` (si non faisable depuis
  l'UI de votre version de CyberPanel).

Tout le reste (déploiements, migrations, cache, cron, SSL, réglages SMTP/
Stripe/veille, gestion des sites sources) se pilote depuis les interfaces
web de CyberPanel et du panel admin Laravel/Filament.
