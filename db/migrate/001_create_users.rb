require 'digest/sha1'
require 'User'

class CreateUsers < ActiveRecord::Migration
  def self.up
    create_table :users do |t|
      t.column :login,        :string,  :null=>false, :default=>""
      t.column :password,     :string,  :null=>false, :default=>User::DEFAULT_PASSWORD
      t.column :firstname,    :string,  :null=>false, :default=>""
      t.column :lastname,     :string,  :null=>false, :default=>""
      t.column :email,        :string,  :null=>false, :default=>""
      t.column :is_admin,     :integer, :null=>false, :default=>0, :limit=>1
      t.column :new_password, :integer, :null=>false, :default=>0, :limit=>1
    end
  end

  def self.down
    drop_table :users
  end
end
