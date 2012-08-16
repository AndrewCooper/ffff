require 'openssl'

module LoginResponseCalculation
  def calculate_login_response( password_hash, challenge )
    OpenSSL::HMAC.hexdigest( OpenSSL::Digest::SHA1.new, password_hash, challenge )
  end
end
