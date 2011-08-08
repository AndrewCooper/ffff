class CreatePicks < ActiveRecord::Migration
  def self.up
    create_table :picks do |t|
      t.column :game_id, :integer, :null=>false, :default=>0
      t.column :user_id, :integer, :null=>false, :default=>0
      t.column :home_score, :integer, :null=>false, :default=>0
      t.column :away_score, :integer, :null=>false, :default=>0
      t.column :pick_score, :integer
      t.column :is_closest, :integer, :null=>false, :limit=>1, :default=>0
    end
    add_index :picks, :game_id
    add_index :picks, :user_id
  end

  def self.down
    drop_table :picks
  end
end
