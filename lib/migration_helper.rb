require 'openssl'

module MigrationHelper

  def default_password
    DEFAULT_PASSWORD
  end

  private

  DEFAULT_PASSWORD = OpenSSL::Digest::SHA1.hexdigest "football!"
end
	
