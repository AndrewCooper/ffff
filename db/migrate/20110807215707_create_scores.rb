class CreateScores < ActiveRecord::Migration
  def self.up
    create_table :scores do |t|
      t.column :user_id, :integer, :null=>false, :default=>0
      t.column :week, :integer, :null=>false, :default=>0
      t.column :wins, :integer, :null=>false, :default=>0
      t.column :closests, :integer, :null=>false, :default=>0
      t.column :perfects, :integer, :null=>false, :default=>0
      t.column :sevens, :integer, :null=>false, :default=>0
      t.column :total, :integer, :null=>false, :default=>0
    end
    add_index :scores, :user_id
  end

  def self.down
    drop_table :scores
  end
end
