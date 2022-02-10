<?php
function pmpro_upgrade_1_7()
{
	pmpro_db_delta();	//just a db delta

	pmpro_setOption("db_version", "1.7");
	return 1.7;
}
