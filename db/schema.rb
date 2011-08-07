# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 7) do

  create_table "bowls", :force => true do |t|
    t.string  "name",       :default => "", :null => false
    t.string  "location",   :default => "", :null => false
    t.integer "game_id",    :default => 0,  :null => false
    t.integer "multiplier", :default => 2,  :null => false
    t.string  "url",        :default => "", :null => false
  end

  add_index "bowls", ["game_id"], :name => "index_bowls_on_game_id"

  create_table "games", :force => true do |t|
    t.integer  "home_team_id",              :default => 0, :null => false
    t.integer  "away_team_id",              :default => 0, :null => false
    t.integer  "home_score"
    t.integer  "away_score"
    t.datetime "gametime",                                 :null => false
    t.integer  "week",                      :default => 1, :null => false
    t.integer  "is_bowl",      :limit => 1, :default => 0, :null => false
  end

  add_index "games", ["away_team_id"], :name => "index_games_on_away_team_id"
  add_index "games", ["home_team_id"], :name => "index_games_on_home_team_id"

  create_table "picks", :force => true do |t|
    t.integer "game_id",                 :default => 0, :null => false
    t.integer "user_id",                 :default => 0, :null => false
    t.integer "home_score",              :default => 0, :null => false
    t.integer "away_score",              :default => 0, :null => false
    t.integer "pick_score"
    t.integer "is_closest", :limit => 1, :default => 0, :null => false
  end

  add_index "picks", ["game_id"], :name => "index_picks_on_game_id"
  add_index "picks", ["user_id"], :name => "index_picks_on_user_id"

  create_table "scores", :force => true do |t|
    t.integer "user_id",  :default => 0, :null => false
    t.integer "week",     :default => 0, :null => false
    t.integer "wins",     :default => 0, :null => false
    t.integer "closests", :default => 0, :null => false
    t.integer "perfects", :default => 0, :null => false
    t.integer "sevens",   :default => 0, :null => false
    t.integer "total",    :default => 0, :null => false
  end

  add_index "scores", ["user_id"], :name => "index_scores_on_user_id"

  create_table "teams", :force => true do |t|
    t.string   "name",       :default => "",                    :null => false
    t.string   "image",      :default => "",                    :null => false
    t.string   "color",      :default => "000000",              :null => false
    t.string   "location",   :default => "",                    :null => false
    t.string   "conference", :default => "",                    :null => false
    t.integer  "rankAP",     :default => 0,                     :null => false
    t.integer  "rankUSA",    :default => 0,                     :null => false
    t.string   "record",     :default => "",                    :null => false
    t.integer  "espnid",     :default => 0,                     :null => false
    t.datetime "updated_on", :default => '2000-01-01 00:00:00', :null => false
  end

  create_table "users", :force => true do |t|
    t.string  "login",                     :default => "",                                         :null => false
    t.string  "password",                  :default => "0d0cbb59296d9acc111f9d04bac586c827724cf1", :null => false
    t.string  "firstname",                 :default => "",                                         :null => false
    t.string  "lastname",                  :default => "",                                         :null => false
    t.string  "email",                     :default => "",                                         :null => false
    t.integer "is_admin",     :limit => 1, :default => 0,                                          :null => false
    t.integer "new_password", :limit => 1, :default => 0,                                          :null => false
    t.integer "alerts",       :limit => 1, :default => 0,                                          :null => false
  end

end
