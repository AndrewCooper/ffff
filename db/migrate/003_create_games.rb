class CreateGames < ActiveRecord::Migration
  def self.up
    create_table :games do |t|
      t.column :home_team_id, :integer, :null=>false, :default=>0
      t.column :away_team_id, :integer, :null=>false, :default=>0
      t.column :home_score, :integer
      t.column :away_score, :integer
      t.column :gametime, :datetime, :null=>false
      t.column :week, :integer, :null=>false, :default=>1
      t.column :is_bowl, :integer, :null=>false, :limit=>1, :default=>0
    end
    add_index :games, :home_team_id
    add_index :games, :away_team_id
  end

  def self.down
    drop_table :games
  end
end
