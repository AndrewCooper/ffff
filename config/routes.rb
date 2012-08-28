FFFF::Application.routes.draw do
  # The priority is based upon order of creation:
  # first created -> highest priority.

  # Sample of regular route:
  #   match 'products/:id' => 'catalog#view'
  # Keep in mind you can assign values other than :controller and :action

  # Sample of named route:
  #   match 'products/:id/purchase' => 'catalog#purchase', :as => :purchase
  # This route can be invoked with purchase_url(:id => product.id)

  # Sample resource route (maps HTTP verbs to controller actions automatically):
  #   resources :products

  # Sample resource route with options:
  #   resources :products do
  #     member do
  #       get 'short'
  #       post 'toggle'
  #     end
  #
  #     collection do
  #       get 'sold'
  #     end
  #   end

  # Sample resource route with sub-resources:
  #   resources :products do
  #     resources :comments, :sales
  #     resource :seller
  #   end

  # Sample resource route with more complex sub-resources
  #   resources :products do
  #     resources :comments
  #     resources :sales do
  #       get 'recent', :on => :collection
  #     end
  #   end

  # Sample resource route within a namespace:
  #   namespace :admin do
  #     # Directs /admin/products/* to Admin::ProductsController
  #     # (app/controllers/admin/products_controller.rb)
  #     resources :products
  #   end

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

  resources :teams, :only => [:index, :show]

  namespace :admin do
    resources :bowls
    resources :games
    resources :teams
    resources :users

    get "database"=>"database#index"
    post "database/reset"=>"database#reset"

    match "picks"=>"picks#index", :via=>:get
    match "picks"=>"picks#update", :via=>:put
    match "picks/user/:user_id"=>"picks#edit", :via=>:get, :as=>"user_picks"
    match "picks/game/:game_id"=>"picks#edit", :via=>:get, :as=>"game_picks"
    match "picks/week/:week_id"=>"picks#edit", :via=>:get, :as=>"week_picks"

    get "score" => "score#index"
    get "score/calculate" => "score#calculate"
  end

  # You can have the root of your site routed with "root"
  # just remember to delete public/index.html.
  # root :to => 'welcome#index'
  root :to => "score#rankings"

  # See how all your routes lay out with "rake routes"

  # This is a legacy wild controller route that's not recommended for RESTful applications.
  # Note: This route will make all actions in every controller accessible via GET requests.
  # match ':controller(/:action(/:id))(.:format)'
end
