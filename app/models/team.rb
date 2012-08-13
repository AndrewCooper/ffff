class Team < ActiveRecord::Base
  has_many :away_games, :class_name => "Game", :foreign_key => "away_team_id"
  has_many :home_games, :class_name => "Game", :foreign_key => "home_team_id"

  attr_accessible :name, :image, :color, :location, :conference, :rankAP, :rankUSA, :record, :espnid, :updated_on

  validates :rankAP, :numericality => { :only_integer => true, :greater_than_or_equal_to => 0 }
  validates :rankUSA, :numericality => { :only_integer => true, :greater_than_or_equal_to => 0 }
  validates :espnid, :numericality => { :only_integer => true, :greater_than_or_equal_to => 0 }

  def fullname
    "#{location} #{name}"
  end
end
