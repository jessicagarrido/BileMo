
# 📱 BileMo API

**Création de l'API Rest BileMo**  
Développé par **Jessica GARRIDO**  
🎓 *Projet de formation Développeuse d'application PHP/Symfony - OpenClassrooms*


---

## 🔧 Environnement de développement

- ⚙️ **Symfony** 7.2.5  
- 📦 **Composer** 2.8.8  
- 💻 **WampServer** 3.3.7  
    - 🌐 Apache 2.4.63  
    - 🐘 PHP 8.2  
    - 🐬 MySQL 8.4.4

---

## 📥 Installation

### 🌀 1. Cloner le repository

```bash
git clone https://github.com/Wickacode/BileMo.git
cd bilemo
```

### 📦 2. Installer les dépendances

```bash
composer install
```

### ⚙️ 3. Configuration de l'environnement

- Copier le fichier `.env` et le renommer en `.env.local`
- Modifier les variables d’environnement, notamment :

```env
# Exemple de configuration de la base de données
DATABASE_URL="mysql://root:@127.0.0.1:3306/nom_de_la_base"
```

> ⚠️ N'oubliez pas de **retirer le `#`** devant la ligne pour qu'elle soit prise en compte.  
> Supprimez le fichier `composer.lock` si présent.

---

## 🔐 Configuration JWT

> ⚠️ Assurez-vous d’avoir l’extension **OpenSSL** activée.

```bash
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

> Une **passphrase** vous sera demandée : gardez-la précieusement.

### Modifier le fichier `.env.local` avec :

```env
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=VotrePassePhrase
###< lexik/jwt-authentication-bundle ###
```

---

## 🛠️ Mise en place de la base de données

### 1. Créer la base

```bash
php bin/console doctrine:database:create
```

### 2. Générer et exécuter la migration

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 3. Charger des données de démonstration (optionnel)

```bash
php bin/console doctrine:fixtures:load
```

---

## 👤 Comptes de démonstration

| Pseudo   | Mot de passe |
|----------|--------------|
| Free   | bilemo2025       |
| SFR      | bilemo2025       |
| Orange | bilemo2025       |

---

## ▶️ Lancement du serveur

```bash
symfony server:start
```

---


## 👨‍💻 Auteur

Jessica GARRIDO
📘 OpenClassrooms - Formation Développeur PHP/Symfony
