class CreateBowls < ActiveRecord::Migration
  def self.up
    create_table :bowls do |t|
      t.column :name, :string, :null=>false, :default=>''
      t.column :location,:string, :null=>false, :default=>''
      t.column :game_id, :integer, :null=>false, :default=>0
      t.column :multiplier, :integer, :null=>false, :default=>2
      t.column :url, :string, :null=>false, :default=>''
    end
    add_index :bowls, :game_id
  end

  def self.down
    drop_table :bowls
  end
end
