class Game < ActiveRecord::Base
	has_one :bowl
	has_many :picks, :dependent=>true
	
	def Game::find_with_teamnames(options = {})
		select = "SELECT games.*, away.name AS away_name, away.location AS away_loc, home.name AS home_name, home.location AS home_loc"
		from = "FROM games LEFT OUTER JOIN teams AS away ON away.id = games.away_team_id LEFT OUTER JOIN teams AS home ON home.id = games.home_team_id"
		where = options[:conditions] ? "WHERE #{options[:conditions]}" : ""
#		where = options[:id] ? "WHERE games.id = #{options[:id]}" : ""
		order = options[:order] ? "ORDER BY #{options[:order]}" : ""
		limit = options[:limit] ? "LIMIT #{options[:limit]}" : ""
		offset = options[:offset] ? "OFFSET #{options[:offset]}" : ""
		Game.find_by_sql(select+" "+from+" "+where+" "+order+" "+limit+" "+offset)
	end
end
