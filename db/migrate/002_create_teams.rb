class CreateTeams < ActiveRecord::Migration
  def self.up
    create_table :teams do |t|
      t.column :name, :string, :null=>false, :default=>''
      t.column :image, :string, :null=>false, :default=>''
      t.column :color, :string, :null=>false, :default=>'000000'
      t.column :location, :string, :null=>false, :default=>''
      t.column :conference, :string, :null=>false, :default=>''
      t.column :rankAP, :integer, :null=>false, :default=>0
      t.column :rankUSA, :integer, :null=>false, :default=>0
      t.column :record, :string, :null=>false, :default=>''
      t.column :espnid, :integer, :null=>false, :default=>0
      t.column :updated_on, :datetime, :null=>false, :default=>'2000-01-01T00:00:00'
    end
  end

  def self.down
    drop_table :teams
  end
end
