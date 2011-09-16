class Pick < ActiveRecord::Base
  belongs_to :user
  belongs_to :game

  validates_numericality_of :home_score, :integer_only=>true, :greater_than_or_equal_to=>0
  validates_numericality_of :away_score, :integer_only=>true, :greater_than_or_equal_to=>0
  validates_numericality_of :pick_score, :integer_only=>true, :greater_than_or_equal_to=>0, :allow_nil=>true

  def calculate_score(gh,ga)
    (home_score - gh).abs + (away_score - ga).abs + ((home_score - away_score).abs - (gh - ga).abs).abs
  end

  def to_s
    "{uid=>#{user_id}, gid=>#{game_id}, hs=>#{home_score}, as=>#{away_score}, ps=>#{pick_score}}"
  end

end
