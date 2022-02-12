class ShoutsController < ApplicationController
	skip_before_action :verify_authenticity_token

	def index
		@shouts = Shout.all
	end

	def new
	end

	def dashboard

		if params.has_key?(:message) and params[:message].present?

			if params[:image].nil?
				image = nil
			else
				image = params[:image].read()
			end

			@shout = Shout.create(
				message: params[:message],
				image: image
			)
		else
			@shout = nil
		end

		@latest_shouts = Shout.order("created_at").last(5).reverse()

		# these do not trigger the RCE
		# @latest_shouts = Rails.cache.fetch_multi(*latest_shout_keys, raw: true, expires_in: 1.days){|x|}
		# Rails.cache.read_multi
		# Rails.cache.read

	end

private
	def shout_params
		params.require(:shout).permit(:title, :text)
	end
end
