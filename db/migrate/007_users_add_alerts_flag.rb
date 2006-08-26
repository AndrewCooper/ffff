class UsersAddAlertsFlag < ActiveRecord::Migration
  def self.up
    add_column(:users, :alerts, :integer, {:limit=>1,:default=>0})
  end

  def self.down
    remove_column(:users, :alerts)
  end
end
