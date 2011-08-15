class Team < ActiveRecord::Base
  has_many :away_games, :class_name => "Game", :foreign_key => "away_team_id"
  has_many :home_games, :class_name => "Game", :foreign_key => "home_team_id"

  def fullname
    "#{location} #{name}"
  end
end
