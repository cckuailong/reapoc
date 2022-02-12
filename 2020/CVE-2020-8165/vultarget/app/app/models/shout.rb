class Shout < ApplicationRecord
	after_create :update_cache_latest

	def cache_key()
		self.class.cache_key(self.id)
	end

	def image
		# fetch image from cache if it exists
		Rails.cache.fetch(self.cache_key, raw: true){
			self[:image]
		}
	end

private
	def self.cache_key(id)
		"shout/#{id}"
	end

	def update_cache_latest
		Rails.cache.write("#{self.cache_key}", self.image)
	end
end
