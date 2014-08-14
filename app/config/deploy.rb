set :application, "PiggyBox"
set :domain,      "_IP_SERVER_GOES_HERE_"
set :deploy_to,   "_FULL_REPO_PATH_GOES_HERE_"
set :app_path,    "app"

set :scm_passphrase, "_PASSPHRASE_GOES_HERE_"
set :password, "_SYSTEM_USER_PASS_GOES_HERE_"

set :repository,  "git@github.com:julienbourdeau/PiggyBox.git"
set :scm,         :git
set :branch, 	  "master" 
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
