class Game < ActiveRecord::Base
  has_one :bowl
  has_many :picks, :dependent=>:delete_all
  belongs_to :away_team, :class_name=>"Team", :foreign_key=>"away_team_id"
  belongs_to :home_team, :class_name=>"Team", :foreign_key=>"home_team_id"

  attr_accessible :away_score, :away_team, :away_team_id, :gametime, :home_score, :home_team, :home_team_id, :is_bowl, :week
  validates :away_score, :numericality => { :only_integer=>:true, :greater_than_or_equal_to=>0, :allow_nil=>:true }
  validates :home_score, :numericality => { :only_integer=>:true, :greater_than_or_equal_to=>0, :allow_nil=>:true }
  validates :week,       :numericality => { :only_integer=>:true, :greater_than_or_equal_to=>1 }
  validates :is_bowl, :inclusion => { :in => [0,1] }

  def fullname
    self.away_team.fullname+" @ "+self.home_team.fullname
  end
end
