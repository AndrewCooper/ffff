FFFF::Application.routes.draw do

  resource :user, :only => [:edit,:update] do
    member do
      get 'forgot_password' => :forgot_password
      put 'forgot_password' => :reset_password
    end
  end


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

  root :to => "score#index"

  # See how all your routes lay out with "rake routes"

  # This is a legacy wild controller route that's not recommended for RESTful applications.
  # Note: This route will make all actions in every controller accessible via GET requests.
  match ':controller(/:action(/:id(.:format)))', :controller=>/admin\/[^\/]+/
  match ':controller(/:action(/:id(.:format)))'
end
