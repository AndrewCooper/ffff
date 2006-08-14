class CreateUsers < ActiveRecord::Migration
  def self.up
    create_table :users do |t|
      t.column :login, :string, :null=>false
      t.column :password, :string, :null=>false
      t.column :firstname, :string, :null=>false
      t.column :lastname, :string, :null=>false
      t.column :email, :string, :null=>false
      t.column :is_admin, :integer, :null=>false, :default=>0
      t.column :new_password, :integer, :null=>false, :default=>0
    end
  end

  def self.down
    drop_table :users
  end
end
