# Installation capifony

## 0. Pr�-requis sur l'environnement client

1. Ruby
2. RubyGems

## 1 Installation de la gem Capifony l'environnement client

```gem install capifony```

## 2 Mise en place de Capifony sur un projet Symfony

Commande � ex�cuter � la racine du projet:

```capifony .```

Capifony se met alors en place sur le projet existant.

## 3 Configuration de Capifony

Capifony cr�e deux fichiers: Capfile � la racine du projet, et /app/config/deploy.rb.

Deploy.rb est le fichier servant � la configuration, et donc le seul � modifier.

Deploy.rb utilis� dans le projet PiggyBox:


```ruby
set :application, "PiggyBox"
set :domain,      "188.165.241.171"
set :deploy_to,   "/home/vsftpd/www/piggybox-v3/PiggyBox"
set :app_path,    "app"

set :scm_passphrase, "The four steps to the epiphany"
set :password, "J1My5DuE"
 
set :repository,  "git@github.com:dupuchba/PiggyBox.git"
set :scm,         :git
set :branch, 	  "feature-capifony" 
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
 
#Commandes effectu�es apr�s le d�ploiement
#Changement du propri�taire
after "deploy", :setup_group
task :setup_group do
  run "chown -R www-data:www-data #{deploy_to}"
end

#Suppression des releases obseletes
after "deploy", "deploy:cleanup"```

### Information sur le fichier de configuration: ###

* **application**: Nom de l'application � mettre en ligne
* **domain**: Adresse du serveur de production
* **deploy_to**: Dossier o� d�ployer l'application 
* **scm_passphrase**: Passphrase utiliser lors de la connexion ssh en fonction de l'utilisateur
* **password**: mot de passe de l'utilisateur (ici root)
* **repository**: Adresse du serveur de versions
* **scm**: Type de serveur de versions
* **branch**: Branch utilis� dans git pour le d�ploiement
* **ssh_options,   :forward_agent => true**: Pour que la connexion ssh � git soit possible
* **shared_files**: Fichiers pr�sent en local sur le serveur � utiliser sur toutes les releases
* **shared_children**: Dossiers pr�sent en local sur le serveur � utiliser sur toutes les releases
* **use_composer, true** et **update_vendors, true**: : Configuration n�cessaire � Symfony
* **web/ap/db**: Emplacements des serveurs web, php, et BDD (ici sur domain soit 188.165.241.171)
* **keep_releases**: Nombres de releases � conserver sur le serveur.
* **logger.level**: Activation du debug, � commenter pour d�sactiver
* Les commandes apr�s le d�ploiements sont le changement de propri�taire de la nouvelle release, ainsi que la suppression des releases obsol�tes.


## 4 Configuration du serveur de production

On installe la Gem:

```gem install capifony```

Puis on se d�place dans le dossier d�finie dans **deploy_to** dans **deploy.rb** , et on ex�cute:

```cap deploy:setup```

Cette commande permet de cr�er l'arborescence n�cessaire au d�ploiement:

* releases
* shared

Releases permet de stocker toutes les releases du site.
Shared permet de garder les fichiers n�cessaires � toutes les releases en liens symboliques (sessions, logs, parameters...)


## 5 Premier d�ploiement

Lors du premier d�ploiement et uniquement lors du premier, il faut �xecuter depuis le client:

```cap deploy:cold```

Elle permet de cr�er la premi�re release sur le serveur et de cr�er le lien symbolique current.

Ce lien sera chang� � chaque nouveau d�ploiement, vers la nouvelle release.

## 6 D�ploiements

Pour d�ployer sur le serveur de production on utilise la commande:

```cap deploy```

Une nouvelle release est alors effectu�e, et le lien symbolique "current" est dirig� vers cette nouvelle release.

## 7 Check

* http://www.cotelettes-tarteauxfraises.com

## 8 Rollback

En cas de dysfonctionnement de la nouvelle release o� que Julien a tout cass�, effectu� la commande:

```cap deploy:rollback```

Cette commande redirigera rapidement le lien current vers la release pr�c�dant cette release d�fectueuse, puis la supprimera.


# 9 Sources
* http://capifony.org/ Site officiel de Capifony avec un bon tuto sur son fonctionnement
* http://capitate.rubyforge.org/recipes/deploy.html Information sur les diff�rentes commandes disponibles
* http://rubygems.org/ Site pour installer RubyGems
