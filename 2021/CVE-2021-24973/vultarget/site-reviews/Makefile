.PHONY: build
build: ## Build all assets and languages
	npm run build

.PHONY: db
db: ## Open the database in TablePlus
	open mysql://dev:dev@127.0.0.1/site-reviews?enviroment=local&name=Localhost&safeModeLevel=0&advancedSafeModeLevel=0

.PHONY: help
help:  ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

.PHONY: open
open: ## Open the development site in the default browser
	open http://site-reviews.test/wp/wp-admin/edit.php?post_type=site-review

.PHONY: phpstan
phpstan: ## Run phpstan analyser
	./vendor/bin/phpstan analyse

.PHONY: phpunit
phpunit: ## Run all phpunit tests
	./vendor/bin/phpunit

.PHONY: release
release: ## Release a new version of Site Reviews
	sh ./release.sh

.PHONY: update
update: ## Update Composer and NPM
	composer update
	npm-check -u

.PHONY: watch
watch: ## Build all plugin assets and run Browsersync
	npm run watch
