Rails.application.routes.draw do
	match 'shouts/dashboard', to: 'shouts#dashboard', via: [:get, :post]
	resources :shouts
	root 'shouts#dashboard'
end
