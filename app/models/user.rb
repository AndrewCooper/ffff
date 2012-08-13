require 'openssl'

class User < ActiveRecord::Base
  has_many :scores, :dependent => :delete_all
  has_many :picks, :dependent => :delete_all

  attr_accessible :login, :password, :firstname, :lastname, :email, :is_admin, :new_password

  before_validation :check_password_updated
  after_validation :digest_updated_password

  validates_uniqueness_of :login, :message => "NOT A UNIQUE LOGIN"
  validates_length_of :login, :within => 3..40
  validates_length_of :password, :within => 5..40
  validates_presence_of :login, :password

  def session_info
    {
      :name=>"#{self[:firstname]} #{self[:lastname]}",
      :uid=>self[:id],
      :admin=>self[:is_admin],
      :stats=>Score.user_stats(self.id)
    }
  end

  def name
    self.firstname+" "+self.lastname
  end

  def self.ranked
    q = select('users.*')
    q = q.joins(:scores)
    q = q.select('SUM(scores.wins) AS wins')
    q = q.select('SUM(scores.closests) AS closests')
    q = q.select('SUM(scores.sevens) AS sevens')
    q = q.select('SUM(scores.perfects) AS perfects')
    q = q.select('SUM(scores.total) AS total')
    q = q.group('users.id')
    q = q.order('total DESC,users.lastname,users.firstname')
  end

  private
  # If the record is updated we will check if the password is empty.
  # If its empty we assume that the user didn't want to change his
  # password and just reset it to the old value.
  def check_password_updated
    if self.new_record? then
      @password_update = false
    else
      if password.empty? then
        user = User.find(self.id)
        self.password = user.password
        @password_update = false
      else
        @password_update = true
      end
    end
    true
  end

  def digest_updated_password
    if @password_update
      self.password = OpenSSL::Digest::SHA1.hexdigest(self.password)
    end
    true
  end

end
