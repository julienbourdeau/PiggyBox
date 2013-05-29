# Installation capifony

## 0. Pré-requis sur l'environnement client

1. Ruby
2. RubyGems

## 1 Installation de la gem Capifony l'environnement client

```gem install capifony```

## 2 Mise en place de Capifony sur un projet Symfony

Commande à exécuter à la racine du projet:

```capifony .```

Capifony se met alors en place sur le projet existant.

## 3 Configuration de Capifony

Capifony crée deux fichiers: Capfile à la racine du projet, et /app/config/deploy.rb.

Deploy.rb est le fichier servant à la configuration, et donc le seul à modifier.

Deploy.rb utilisé dans le projet PiggyBox:

```ruby
set :application, "PiggyBox"
set :domain,      "188.165.241.171"
set :deploy_to,   "/home/vsftpd/www/piggybox-v3/PiggyBox"
set :app_path,    "app"

set :scm_passphrase, "The four steps to the epiphany"
set :password, "J1My5DuE"

set :repository,  "git@github.com:dupuchba/PiggyBox.git"
set :scm,         :git
set :branch,    "feature-capifony" 
set :ssh_options,   :forward_agent => true
set :shared_files,      ["app/config/parameters.yml", "web/.htaccess"]
set :shared_children,     [app_path + "/logs", app_path + "/sessions", web_path + "/uploads", web_path + "/media"]
set :use_composer, true
set :update_vendors, true

# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Symfony2 migrations will run

set  :keep_releases,  10

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL
 
#Commandes effectuées après le déploiement
#Changement du propriétaire
after "deploy", :setup_group
task :setup_group do
  run "chown -R www-data:www-data #{deploy_to}"
end

#Suppression des releases obseletes
after "deploy", "deploy:cleanup"
```

### Information sur le fichier de configuration: ###

* **application**: Nom de l'application à mettre en ligne
* **domain**: Adresse du serveur de production
* **deploy_to**: Dossier où déployer l'application 
* **scm_passphrase**: Passphrase utiliser lors de la connexion ssh en fonction de l'utilisateur
* **password**: mot de passe de l'utilisateur (ici root)
* **repository**: Adresse du serveur de versions
* **scm**: Type de serveur de versions
* **branch**: Branch utilisé dans git pour le déploiement
* **ssh_options,   :forward_agent => true**: Pour que la connexion ssh à git soit possible
* **shared_files**: Fichiers présent en local sur le serveur à utiliser sur toutes les releases
* **shared_children**: Dossiers présent en local sur le serveur à utiliser sur toutes les releases
* **use_composer, true** et **update_vendors, true**: : Configuration nécessaire à Symfony
* **web/ap/db**: Emplacements des serveurs web, php, et BDD (ici sur domain soit 188.165.241.171)
* **keep_releases**: Nombres de releases à conserver sur le serveur.
* **logger.level**: Activation du debug, à commenter pour désactiver
* Les commandes après le déploiements sont le changement de propriétaire de la nouvelle release, ainsi que la suppression des releases obsolètes.


## 4 Configuration du serveur de production

On installe la Gem:

```gem install capifony```

Puis on se déplace dans le dossier définie dans **deploy_to** dans **deploy.rb** , et on exécute:

```cap deploy:setup```

Cette commande permet de créer l'arborescence nécessaire au déploiement:

* releases
* shared

Releases permet de stocker toutes les releases du site.
Shared permet de garder les fichiers nécessaires à toutes les releases en liens symboliques (sessions, logs, parameters...)


## 5 Premier déploiement

Lors du premier déploiement et uniquement lors du premier, il faut éxecuter depuis le client:

```cap deploy:cold```

Elle permet de créer la première release sur le serveur et de créer le lien symbolique current.

Ce lien sera changé à chaque nouveau déploiement, vers la nouvelle release.

## 6 Déploiements

Pour déployer sur le serveur de production on utilise la commande:

```cap deploy```

Une nouvelle release est alors effectuée, et le lien symbolique "current" est dirigé vers cette nouvelle release.

## 7 Check

* http://www.cotelettes-tarteauxfraises.com

## 8 Rollback

En cas de dysfonctionnement de la nouvelle release où que Julien a tout cassé, effectué la commande:

```cap deploy:rollback```

Cette commande redirigera rapidement le lien current vers la release précédant cette release défectueuse, puis la supprimera.


# 9 Sources
* http://capifony.org/ Site officiel de Capifony avec un bon tuto sur son fonctionnement
* http://capitate.rubyforge.org/recipes/deploy.html Information sur les différentes commandes disponibles
* http://rubygems.org/ Site pour installer RubyGems
