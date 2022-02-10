<?php

/**
 * This file exists because versions prior to v4.3.8 triggered the 
 * upgrader_process_complete hook during a plugin update which had the 
 * undesired side-effect of running the upgrade process while the plugin was 
 * still in a "dirty" state.
 *
 * Since this directory was removed in v4.3.8, and since versions prior to 
 * v4.3.0 did not check if the Upgrades folder existed before attempting to 
 * iterate the contents, upgrading from versions prior to v4.3.0 has the 
 * potential to throw an UnexpectedValueException error.
 */
