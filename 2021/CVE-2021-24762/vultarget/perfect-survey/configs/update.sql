UPDATE {{table_prefix}}ps SET version = '{{version}}';$$$
ALTER TABLE `{{table_prefix}}ps_data` MODIFY `value` VARCHAR (1000) DEFAULT NULL;$$$
ALTER TABLE `{{table_prefix}}ps_data` MODIFY `ip` VARCHAR (255) DEFAULT NULL;$$$
ALTER TABLE `{{table_prefix}}ps_data` ADD `cookie_id` VARCHAR(255) NULL DEFAULT NULL AFTER `session_id`, ADD INDEX (`cookie_id`);$$$
