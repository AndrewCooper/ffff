FFFF::Application.routes.draw do
  get  "login"  => "login#request_login"
  post "login"  => "login#login"
  get  "logout" => "login#logout"

  resource :user, :only => [:edit,:update] do
    member do
      get 'forgot_password' => :forgot_password
      put 'forgot_password' => :reset_password
    end
  end

  get "picks"=>"picks#index", :as=>"picks"
  put "picks"=>"picks#update", :as=>"picks"
  get "picks/edit"=>"picks#edit", :as=>"edit_picks"

  get "score"=>"score#index", :as=>"scores"
  get "score/rankings"=>"score#rankings", :as=>"rankings"

  resources :teams, :only => [:show]

  namespace :admin do
    resources :bowls
    resources :games
    resources :teams
    resources :users

    match "picks"=>"picks#index", :via=>:get
    match "picks"=>"picks#update", :via=>:put
    match "picks/user/:user_id"=>"picks#edit", :via=>:get, :as=>"user_picks"
    match "picks/game/:game_id"=>"picks#edit", :via=>:get, :as=>"game_picks"
    match "picks/week/:week_id"=>"picks#edit", :via=>:get, :as=>"week_picks"
  end

  root :to => "score#rankings"

  # See how all your routes lay out with "rake routes"

  # This is a legacy wild controller route that's not recommended for RESTful applications.
  # Note: This route will make all actions in every controller accessible via GET requests.
  match ':controller(/:action(/:id(.:format)))', :controller=>/admin\/[^\/]+/
  match ':controller(/:action(/:id(.:format)))'
end
