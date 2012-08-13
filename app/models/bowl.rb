class Bowl < ActiveRecord::Base
  belongs_to :game
  attr_accessible :name, :location, :multiplier, :url, :game_id
  validates :multiplier, :numericality => { :only_integer => true, :greater_than => 0 }
end
