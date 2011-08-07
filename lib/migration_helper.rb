require 'digest/sha1'

module MigrationHelper

  def default_password
    DEFAULT_PASSWORD
  end

  private

  DEFAULT_PASSWORD = Digest::SHA1.hexdigest "football!"
end
	
