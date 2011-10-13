class Bowl < ActiveRecord::Base
  belongs_to :game

  validates :multiplier, :numericality => { :only_integer => true, :greater_than => 0 }
end
