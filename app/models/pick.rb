class Pick < ActiveRecord::Base
	belongs_to :user
	belongs_to :game
	
	def calculate_score(gh,ga)
		(home_score - gh).abs + (away_score - ga).abs + ((home_score - away_score).abs - (gh - ga).abs).abs
	end
	
	def to_s
		"{uid=>#{user_id}, gid=>#{game_id}, hs=>#{home_score}, as=>#{away_score}, ps=>#{pick_score}}"
	end
end
