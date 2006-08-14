class CreateTeams < ActiveRecord::Migration
  def self.up
    create_table :teams do |t|
      t.column :name, :string, :null=>false
      t.column :image, :string, :null=>false
      t.column :color, :string, :null=>false, :limit=>6
      t.column :location, :string, :null=>false
      t.column :conference, :string, :null=>false
      t.column :rankAP, :integer, :null=>false, :default=>0
      t.column :rankUSA, :integer, :null=>false, :default=>0
      t.column :record, :string, :null=>false
      t.column :espnid, :integer, :null=>false, :default=>0
      t.column :updated_on, :datetime, :null=>false
    end
  end

  def self.down
    drop_table :teams
  end
end
