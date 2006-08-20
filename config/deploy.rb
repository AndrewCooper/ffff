# Introductory SwitchTower config for Dreamhost.
#
# ORIGINAL BY: Jamis Buck
#
# Docs: http://manuals.rubyonrails.com/read/chapter/98
#
# HIGHLY MODIFIED BY: Geoffrey Grosenbach boss@topfunky.com
#
# Docs: http://nubyonrails.com/pages/shovel_dreamhost
#
# USE AT YOUR OWN RISK! THIS SCRIPT MODIFIES FILES, MAKES DIRECTORIES, AND STARTS
# PROCESSES. FOR ADVANCED OR DARING USERS ONLY!
#
# DESCRIPTION
#
# This is a customized recipe for easily deploying web apps to a shared host.
#
# You also need to modify Apache's document root using Dreamhost's web control panel.
#
# For full details, see http://nubyonrails.com/pages/shovel_dreamhost
#
# To setup lighty, first edit this file for your primary Dreamhost account.
#
# Then run:
#   rake remote:exec ACTION=setup
#
# This will create all the necessary directories for running Switchtower.
#
# From then, you can deploy your application with Switchtower's standard
#   rake deploy
#
# Or rollback with
#   rake rollback
# 
# This defines a deployment "recipe" that you can feed to switchtower
# (http://manuals.rubyonrails.com/read/book/17). It allows you to automate
# (among other things) the deployment of your application.
#
# NOTE: When editing on Windows, be sure to save with Unix line endings (LF).

set :user, 'acooper'
set :application, "ffff"
set :repository, "file:///home/#{user}/svn.hkcreations.org/#{application}/trunk"
# NOTE: If file:/// doesn't work for you, try this:
#set :repository, "svn+ssh://home/#{user}/svn/#{application}"


# =============================================================================
# You shouldn't have to modify the rest of these
# =============================================================================

role :web, application
role :app, application
role :db,  application, :primary => true

set :deploy_to, "/home/#{user}/#{application}.hkcreations.org"
# set :svn, "/path/to/svn"       # defaults to searching the PATH
set :use_sudo, false

desc "Restart the FCGI processes on the app server as a regular user."
task :restart, :roles => :app do
  run "ruby #{current_path}/script/process/reaper --dispatcher=dispatch.fcgi"
end

# In config/deploy.rb
desc "Setup the application to run in production on Dreamhost"
task :after_symlink, :roles => [:web, :app] do
	# Force production enviroment by replacing a line in environment.rb
	run "perl -i -pe \"s/#ENV\\['RAILS_ENV'\\] \\|\\|= 'production'/ENV['RAILS_ENV'] ||= 'production'/\" #{current_path}/config/environment.rb"
  
  # Make dispatcher executable
  run "chmod a+x #{current_path}/public/dispatch.fcgi"
  
  # Link to the logos directory
  run "ln -s #{deploy_to}/logos #{current_path}/public/logos"
end