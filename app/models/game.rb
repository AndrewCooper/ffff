class Game < ActiveRecord::Base
  has_one :bowl
  has_many :picks, :dependent=>:delete_all
  belongs_to :away_team, :class_name=>"Team", :foreign_key=>"away_team_id"
  belongs_to :home_team, :class_name=>"Team", :foreign_key=>"home_team_id"

  def fullname
    self.away_team.fullname+" @ "+self.home_team.fullname
  end
end
