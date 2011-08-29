class UsersAddAlertsFlag < ActiveRecord::Migration
  def self.up
    add_column(:users, :alerts, :integer, {:null=>false, :default=>0, :limit=>1})
  end

  def self.down
    remove_column(:users, :alerts)
  end
end
