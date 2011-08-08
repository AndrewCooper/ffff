# This file should contain all the record creation needed to seed the database with its default values.
# The data can then be loaded with the rake db:seed (or created alongside the db with db:setup).
#
# Examples:
#
#   cities = City.create([{ :name => 'Chicago' }, { :name => 'Copenhagen' }])
#   Mayor.create(:name => 'Daley', :city => cities.first)

admin = User.find_or_create_by_login( :login=>"admin", 
                                      :password=>"ffff@dm1n", 
                                      :firstname=>"FFFF", 
                                      :lastname=>"Admin", 
                                      :email=>"admin@example.com",
                                      :is_admin=>1,
                                      :new_password=>1,
                                      :alerts=>0 )

