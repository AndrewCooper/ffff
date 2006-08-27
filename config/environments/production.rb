# Settings specified here will take precedence over those in config/environment.rb

# The production environment is meant for finished, "live" apps.
# Code is not reloaded between requests
config.cache_classes = true

# Use a different logger for distributed setups
# config.logger = SyslogLogger.new

# Full error reports are disabled and caching is turned on
config.action_controller.consider_all_requests_local = false
config.action_controller.perform_caching             = true

# Enable serving of images, stylesheets, and javascripts from an asset server
# config.action_controller.asset_host                  = "http://assets.example.com"

# Disable delivery errors if you bad email addresses should just be ignored
# config.action_mailer.raise_delivery_errors = false

FFFF_BACKUP_DIR = "~/backups/rails/"
FFFF_LOGOS_DIR = "~/ffff.hkcreations.org/logos/"

# Include your app's configuration here:
ActionMailer::Base.server_settings = {
  :address  => "mail.hkcreations.org",
  :port  => 587, 
  :domain  => "ffff.hkcreations.org",
  :user_name  => "m8475547",
  :password  => "*gw!oC",
  :authentication  => :login
}