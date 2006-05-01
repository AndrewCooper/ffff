require 'digest/sha1'

class User < ActiveRecord::Base
  has_many :scores, :dependent => true
  has_many :picks, :dependent => true
  
  def session_info
    {:name=>"#{self[:firstname]} #{self[:lastname]}",:uid=>self[:id],:admin=>self[:admin]}
  end

  protected

  # If the record is updated we will check if the password is empty.
  # If its empty we assume that the user didn't want to change his
  # password and just reset it to the old value.
  def before_validation
    if password.empty?      
      if self.new_record?
        self.password = "football!"
        @password_update=true
      else
        user = User.find(self.id)
        self.password = user.password
        @password_update=false
      end
    else
      @password_update=true
    end
  end

  def after_validation
    if @password_update
      self.password = Digest::SHA1.hexdigest(self.password)
    end
  end

  validates_uniqueness_of :login
  validates_length_of :login, :within => 3..40
  validates_length_of :password, :within => 5..40
  validates_presence_of :login, :password
end
